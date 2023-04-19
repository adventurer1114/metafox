/**
 * @type: ui
 * name: error.fallback.some_thing_wrong
 */
import React from 'react';

export interface ErrorFallbackProps {
  error: Error;
  errorInfo: React.ErrorInfo;
}

export default function ErrorFallback({
  error,
  errorInfo
}: ErrorFallbackProps) {
  return (
    <div data-testid="errorBoundary">
      <h2>Something went wrong.</h2>
      <details style={{ whiteSpace: 'pre-wrap' }}>
        {error && error.toString()}
        <br />
        {errorInfo && errorInfo.componentStack}
      </details>
    </div>
  );
}
