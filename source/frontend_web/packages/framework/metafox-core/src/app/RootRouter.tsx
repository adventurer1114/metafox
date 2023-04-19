/**
 * @type: service
 * name: RootRouter
 */
import React from 'react';
import { BrowserRouter, MemoryRouter } from 'react-router-dom';
import { useGlobal } from '..';

export default function RootRouter({ children, test, routerProps }) {
  const Router: any = test ? MemoryRouter : BrowserRouter;

  const mn = useGlobal();
  const { getConfig } = mn;

  const basename = getConfig('root.env.PUBLIC_URL');

  return (
    <Router basename={basename} {...routerProps}>
      {children}
    </Router>
  );
}
