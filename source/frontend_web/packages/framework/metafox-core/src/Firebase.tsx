/**
 * @type: service
 * name: firebaseBackend
 */
import { Manager } from '@metafox/framework';
import { initializeApp } from 'firebase/app';
import { getFirestore } from 'firebase/firestore';
import {
  getAuth,
  signInWithEmailAndPassword,
  createUserWithEmailAndPassword
} from 'firebase/auth';
import { getMessaging } from 'firebase/messaging/sw';

export default class FirebaseBackend {
  private firestore;
  private config;
  private mounted;
  private settingApp;
  private cloudMessage;
  private firebaseApp;
  private isActive: boolean;
  /**
   * See manager pattern
   */
  private manager: Manager;
  public bootstrap(manager) {
    this.manager = manager;
    this.settingApp = this.manager.getSetting('firebase');

    if (this.settingApp) {
      this.init(this.settingApp);
    }
  }
  public checkActive() {
    return this.isActive;
  }
  public init(config) {
    // const { dispatch } = this.manager;

    if (this.mounted) return this.firebaseApp;

    this.mounted = true;

    const firebaseConfig = {
      apiKey: config?.api_key,
      authDomain: config?.auth_domain,
      projectId: config?.project_id,
      storageBucket: config?.storage_bucket,
      messagingSenderId: config?.sender_id,
      appId: config?.app_id
    };

    this.config = config;
    // Initialize Firebase
    try {
      const app = initializeApp(firebaseConfig);
      const auth = getAuth(app);
      const { user_firebase_email, user_firebase_password, requiredSignin } =
        this.settingApp || {};

      if (requiredSignin) {
        signInWithEmailAndPassword(
          auth,
          user_firebase_email,
          user_firebase_password
        )
          .then(data => {})
          .catch(error => {
            console.log(
              'Live Video - Firebase signInWithEmailAndPassword error',
              error
            );

            if (error.code === 'auth/user-not-found') {
              createUserWithEmailAndPassword(
                auth,
                user_firebase_email,
                user_firebase_password
              );
            }
          });
      }

      this.firebaseApp = app;
      this.isActive = true;

      return app;
    } catch (error) {
      this.isActive = false;
    }
  }
  public getFirebaseApp() {
    return this.firebaseApp;
  }
  public getFirestore() {
    if (!this.firebaseApp) return null;

    if (!this.firestore) {
      const db = getFirestore(this.firebaseApp);

      if (db) {
        this.firestore = db;
      }
    }

    return this.firestore;
  }

  public getMessaging() {
    if (!this.firebaseApp) return null;

    if (!this.cloudMessage) {
      const message = getMessaging(this.firebaseApp);

      if (message) {
        this.cloudMessage = message;
      }
    }

    return this.cloudMessage;
  }

  public checkNewLiveStream() {
    if (!this.firestore) {
      return false;
    }
  }
}
