# Data Migration Json Schema

gist: https://gist.github.com/jacky158/555d2c90bb6de53f8b3abffe4ec2e4d9

**Challenges**

- Không thể migrate trực tiếp từ database => database vì khác biệt về thiết kế, các concepts của v4 <> v5.
- Import live không khả thi vì hạn chế resource trên server và phải cài đặt nhiều source code lên lên metafox
- Khó maintanance script dùng để import.

Hướng giải quyết là dùng một định dạng chung để mô tả dữ liệu cần import vào metafox.

quy trình : phpfov4 -> xuất file phpfox.data.zip => import vào metafox platform.

Ưu điểm:

- Tận dụng cơ chế Model Factory, Schedule, Queue của metafox.
- Quản lý thống nhất các nguồn source cần cung cấp dữ liệu.
- Mô tả được quan hệ giữa các item được import và thứ tự tốt hơn.

Nhược điểm:

- Cần hiểu được các concept của metafox để tạo morph map các dữ liệu một cách hơp lý.

Concepts:

- "user": person who created the content, ví dụ:
- "owner": chủ sở hữu của content
- "file": file item của content

reference content: "{type}#{uniq_id}"

ví dụ:

"user#1" // unix by id, reference tới user(id=1) từ bảng dữ liệu của phpfox.

**file**
đối với trường hợp của file, external resource sẽ map thành
file#md5(path) và được mapming sau khi đã xử lý như sau:
storage: storage system to map etc: format "phpfox:{server}", etc: "phpfox:0"

```php

// fetch from database offset 0, 500 etc.
function export($resource, $page, $data, $processItem){
    $file = 'data/user/1.json'; // first pages, order by id.
    $entries = array_map($processItem, $users);

    file_put_contents($file, json_encode($entries, JSON_PRETTY_PRINT));
}
```

```json
{
    "$id": "file#9fa09463c063222085085c050c86a8ca",
    "origin": "PF.Base/file/pic/event/2021/04/6d31b907b70e158364ddac0fb963e75f.png",
    "storage": "phpfox:0",
    "variants": [
        {
            "variant": "200",
            "storage": "phpfox:0",
            "path": "PF.Base/file/pic/event/2021/04/6d31b907b70e158364ddac0fb963e75f_200_square.png"
        },
        {
            "variant": "500",
            "path": "PF.Base/file/pic/event/2021/04/6d31b907b70e158364ddac0fb963e75f_500.png"
        }
    ]
}
```

tổ chức thư mục:

```txt
schedule.json
data/
  user/
    1.json # Chia thành từng phần (100-500) items để thuận tiện hơn.
    2.json
    3.json
```

** schedule.json**

```json
{
    "source": "phpfox:v4",
    "data": [
        {
            "priotity": 1,
            "resource": "user",
            "path": "data/user"
        },
        {
            "priotity": 2,
            "resource": "blog",
            "path": "data/blog"
        }
    ]
}
```

file schedule.json hướng dẫn để metafox import data ra vào & các thứ tự các resource để import.

```text
file schema dùng để chứa hướng dẫn từ nguồn nào được import vào.

```

## Example

```json
[
    {
        "$id": "blog#1",
        "$owner": "user#1",
        "$user": "user#1",
        "$avatar": {
            "$id": "file#9fa09463c063222085085c050c86a8ca",
            "origin": "PF.Base/file/pic/event/2021/04/6d31b907b70e158364ddac0fb963e75f.png",
            "storage": "phpfox:0",
            "variants": [
                {
                    "variant": "200",
                    "storage": "phpfox:0",
                    "path": "PF.Base/file/pic/event/2021/04/6d31b907b70e158364ddac0fb963e75f_200_square.png"
                },
                {
                    "variant": "500",
                    "storage": "phpfox:0",
                    "path": "PF.Base/file/pic/event/2021/04/6d31b907b70e158364ddac0fb963e75f_500.png"
                }
            ]
        },
        "$cover": {
            "$id": "file#9fa09463c063222085085c050c86a8ca",
            "origin": "PF.Base/file/pic/event/2021/04/6d31b907b70e158364ddac0fb963e75f.png",
            "storage": "phpfox:0",
            "variants": [
                {
                    "variant": "200",
                    "storage": "phpfox:0",
                    "path": "PF.Base/file/pic/event/2021/04/6d31b907b70e158364ddac0fb963e75f_200_square.png"
                },
                {
                    "variant": "500",
                    "storage": "phpfox:0",
                    "path": "PF.Base/file/pic/event/2021/04/6d31b907b70e158364ddac0fb963e75f_500.png"
                }
            ]
        },
        "title": "[title name]",
        "createdAt": "2022-11-16 22:53:09",
        "updatedAt": "2022-11-16 22:53:09"
    }
]
```

## Users

```json
[
    {
        "$id": "user#1",
        "$role": "user_group_id#4",
        "user_name": "string",
        "full_name": "string",
        "first_name": "string",
        "last_name": "string",
        "email": "string",
        "email_verified_at": "timestamp|nullable",
        "is_featured": "timestamp|nullable",
        "is_invisible": "0|1",
        "created_at": "timestamp|nullable",
        "updated_at": "timestamp|nullable",
        "is_approved": "0|1",
        "featured_at": "timestamp|nullable"
    }
]
```

#### User Password

```json
[
    {
        "$id": "user#1",
        "source": "phpfox",
        "algorithm": "string",
        "hash": "string",
        "token": "string",
        "expires_at": "timestamp|nullable"
    }
]
```

algorithm, hash, token, source is preserver data for user can login back by your defined plugin later.

## Friends

```json
[
    {
        "$id": "friend#1",
        "$user": "user#1",
        "$owner": "owner#1",
        "created_at": "timestamp|nullable",
        "updated_at": "timestamp|nullable"
    }
]
```

### Friend List

```json
[
    {
        "$id": "friend#1",
        "$user": "user#1",
        "name": "string",
        "created_at": "timestamp|nullable",
        "updated_at": "timestamp|nullable"
    }
]
```

### Friend List

```json
[
    {
        "$id": "friend#1",
        "$user": "user#1",
        "name": "string",
        "created_at": "timestamp|nullable",
        "updated_at": "timestamp|nullable"
    }
]
```

### Friend List Data

```json
[
    {
        "$list": "ref|required",
        "$user": "user#4",
        "created_at": "timestamp|nullable",
        "updated_at": "timestamp|nullable"
    }
]
```

### Friend Requests

```json
[
    {
        "$id": "ref",
        "$user": "ref",
        "$onwer": "ref",
        "status_id": "int",
        "is_deny": "0|1",
        "created_at": "timestamp|nullable",
        "updated_at": "timestamp|nullable"
    }
]
```

### Blogs

```json
[
    {
        "$id": "blog#1",
        "$files": [],
        "$user": "user#1",
        "$owner": "user#1",
        "module_id": "blog",
        "package_id": "metafox/blog",
        "privacy": "$privacy",
        "total_view": "number",
        "total_like": "number",
        "total_comment": "number",
        "total_reply": "number",
        "total_share": "number",
        "total_attachment": "number",
        "title": "string",
        "is_draft": "0|1",
        "is_featured": "0|1",
        "featured_at": "timestamp|nullable",
        "is_sponsor": "0|1",
        "sponsor_in_feed": "0|1",
        "is_approved": "0|1",
        "image_file_id": "0|1",
        "tags": "string separted by comma",
        "created_at": "timestamp|nullable",
        "updated_at": "timestamp|nullable"
    }
]
````

## Comment

```json
[
    {
        "$id": "comment#3",
        "$owner": "user#1",
        "$user": "#user#1",
        "$parent": "comment#1",
        "$item": "",
        "$tagged_user_ids": [
            "user#1",
            "user#2"
        ],
        "is_approved": "0|1",
        "is_spam": "0|1",
        "module_id": "comment",
        "package_id": "metafox/comment",
        "total_comment": "int",
        "total_like": "int",
        "text": "",
        "text_parsed": "string",
        "created_at": "2022-11-16 22:53:09",
        "updated_at": "2022-11-16 22:53:09"
    }
]
```

**commment reply**

Impoter solve comment#2 when insert into database.

```json
[
    {
        "$id": "comment#1000",
        "$parent": "comment#2",
        "$owner": "user#1",
        "$user": "#user#1",
        "content": "[title name]",
        "created_at": "2022-11-16 22:53:09",
        "updated_at": "2022-11-16 22:53:09"
    }
]
```
