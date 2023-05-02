<?php

$allure_server           = getenv('ALLURE_SERVER');
$project_id              = getenv('ALLURE_PROJECT');
$allure_result_directory = './build/allure-results';

if (!$project_id) {
    $project_id = 'phpunit';
}

function generateReport($directory, $allure_server, $project_id)
{
    //#Generate report
    $ch = curl_init(sprintf(
        '%s/allure-docker-service/generate-report?project_id=%s&execution_name=phpunit',
        $allure_server,
        $project_id
    ));

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $json     = json_decode($response, true);

    //var_export(array_keys($response));
    curl_close($ch);

    if (is_array($json) && array_key_exists('data', $json)) {
        echo 'report_url ' . $json['data']['report_url'];
        exec('rm -rf ' . $directory);
    } else {
        echo $response;
    }
}

function sendAllureResults($directory, $allure_server, $project_id)
{
    if (!$allure_server) {
        echo "Missed ALLURE_SERVER";
        exit(1);
    }

    if (!is_dir($directory)) {
        exit(1);
    }

    /** @var SplFileInfo[] $iterator */
    $iterator = new FilesystemIterator(
        $directory,
        FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS
    );

    $payload = ['results' => []];
    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }
        $payload['results'][] = [
            'file_name'      => $file->getBasename(),
            'content_base64' => base64_encode(file_get_contents($file->getPathname())),
        ];
    }

    //# send result
    $ch = curl_init();
    curl_setopt(
        $ch,
        CURLOPT_URL,
        sprintf('%s/allure-docker-service/send-results?project_id=%s', $allure_server, $project_id)
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['content-type:application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    $total = count($payload['results']);
    unset($payload['results']); // save memory

    echo "Send {$total} files.";

    generateReport($directory, $allure_server, $project_id);
}

sendAllureResults($allure_result_directory, $allure_server, $project_id);

//response = requests.post(allure_server + '/allure-docker-service/send-results?project_id=' + project_id, headers=headers, data=json_request_body, verify=ssl_verification)
