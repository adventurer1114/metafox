/**
 * @type: route
 * name: core.error404
 * path: /page-not-found
 */
import { Page } from '@metafox/layout';
import * as React from 'react';

export default function ErrorPage() {
  return <Page pageName="core.error404" />;
}
