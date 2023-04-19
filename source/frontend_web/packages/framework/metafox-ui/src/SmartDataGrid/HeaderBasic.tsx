/**
 * @type: ui
 * name: dataGrid.header.Basic
 */

import HeaderCell from './HeaderCell';
import React from 'react';

function HeaderBasic({ colDef }) {
  return <HeaderCell colDef={colDef}>{colDef.headerName ?? ''}</HeaderCell>;
}

export default HeaderBasic;
