/**
 * @type: service
 * name: toastBackend
 */
import { ToastBackendConfig, ToastBroker, ToastItemShape } from './types';

export default class ToastBackend {
  config: ToastBackendConfig;

  public static readonly configKey: string = 'toast';

  private broker: ToastBroker;

  constructor(config: Partial<ToastBackendConfig>) {
    this.config = Object.assign(
      {
        position: 'left|bottom',
        error: { duration: 10e3 },
        success: { duration: 5e3 },
        info: { duration: 5e3 },
        warning: { duration: 5e3 }
      },
      config
    );
  }

  public setBroker(broker: ToastBroker) {
    this.broker = broker;
  }

  public bootstrap() {}

  public show(toast: ToastItemShape) {
    if (this.broker) {
      this.broker(toast);
    }
  }

  public info(message: string): void {
    this.show(
      Object.assign(
        {
          message,
          severity: 'info'
        },
        this.config.info
      )
    );
  }

  public warning(message: string): void {
    this.show(
      Object.assign(
        {
          message,
          severity: 'warning'
        },
        this.config.warning
      )
    );
  }

  public error(message: string): void {
    this.show(
      Object.assign(
        {
          message,
          severity: 'error'
        },
        this.config.error
      )
    );
  }

  public success(message: string): void {
    this.show(
      Object.assign(
        {
          message,
          severity: 'success'
        },
        this.config.success
      )
    );
  }
}
