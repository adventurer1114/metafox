import { useGlobal } from '@metafox/framework';
import * as React from 'react';
import { Helmet } from 'react-helmet-async';

export default function HelmetData() {
  const { usePageMeta } = useGlobal();
  const data = usePageMeta();
  const names = ['keywords', 'description', 'twitter:card', 'robots'];
  const properties = [
    'og:site_name',
    'og:title',
    'og:description',
    'og:url',
    'og:type',
    'og:updated_time',
    'og:image',
    'og:video',
    'og:audio',
    'fb:app_id',
    'og:image:alt'
  ];

  return (
    <Helmet>
      <title>{data?.title}</title>
      {names.map(key => {
        return data[key] ? (
          <meta name={key} key={key} content={data[key]} />
        ) : null;
      })}
      {properties.map(key => {
        return data[key] ? (
          <meta key={key} name={key} content={data[key]} />
        ) : null;
      })}
    </Helmet>
  );
}
