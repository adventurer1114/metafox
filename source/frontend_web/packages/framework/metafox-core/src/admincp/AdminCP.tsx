import React from 'react';
import RootContainer from '../app/RootContainer';
import Template from './Template';
import config from '@metafox/react-app/bundle-admincp/config';

type AppProps = {
  test?: boolean;
};

// chore: pre-commit-test 5
export default function AdminCP(props: AppProps) {
  return <RootContainer config={config} template={Template} />;
}
