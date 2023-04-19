/**
 * @type: block
 * name: ad.block.sampleAd
 */

import { useGlobal } from '@metafox/framework';
import { range } from 'lodash';
import React from 'react';

export default function SampleAdBlock() {
  const { popoverBackend } = useGlobal();

  return (
    <div style={{ padding: '32px' }}>
      <div>
        {range(1, 10).map(x => (
          <a
            key={x.toString()}
            href={`/user/${x}`}
            onMouseEnter={popoverBackend.onEnterAnchor}
            onMouseLeave={popoverBackend.onLeaveAnchor}
          >
            [User 1]
          </a>
        ))}
      </div>
    </div>
  );
}
