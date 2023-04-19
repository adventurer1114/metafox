import { getToken, Messaging } from 'firebase/messaging';
import React from 'react';
import { initializeApp } from 'firebase/app';
import { getMessaging } from 'firebase/messaging/sw';
import useGlobal from './useGlobal';

export type LoadingHook<T, E> = [T | undefined, boolean, E | undefined];

let messaging: Messaging;
type CallBackType = (x: string) => void;

export default function useFirebaseFCM(): [
  string,
  (x: CallBackType) => void,
  boolean
] {
  const { getSetting } = useGlobal();
  const [token, setToken] = React.useState<string>();
  const [error, setError] = React.useState(false);
  const firebaseSettings: Record<string, any> = getSetting('firebase');

  const firebaseConfig = {
    apiKey: firebaseSettings?.api_key,
    authDomain: firebaseSettings?.auth_domain,
    projectId: firebaseSettings?.project_id,
    storageBucket: firebaseSettings?.storage_bucket,
    messagingSenderId: firebaseSettings?.sender_id,
    appId: firebaseSettings?.app_id
  };

  if (!firebaseSettings?.api_key) return ['', () => {}, true];

  if (!messaging && !error) {
    try {
      messaging = getMessaging(initializeApp(firebaseConfig));
    } catch (err) {
      setError(true);
    }
  }

  const handleGetToken = (callback: CallBackType) =>
    getToken(messaging).then(data => {
      callback(data);
      setToken(data);
    });

  return [token, handleGetToken, error];
}
