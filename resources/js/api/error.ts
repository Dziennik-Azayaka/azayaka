import type { FetchResponse } from 'ofetch';

export class ApiError {
  constructor(
    public code: string,
    public httpStatus: number | null,
  ) {}

  static fromResponse(response: FetchResponse<{ errors: string[] }>) {
    const code = response._data?.errors?.[0] ?? 'UNKNOWN_ERROR';
    const httpStatus = response.status;
    return new ApiError(code, httpStatus);
  }

  getTranslationId() {
    switch (this.code) {
      case 'CODE_NOT_FOUND':
        return 'apiErrors.incorrectCode';
      case 'INVALID_USERNAME_OR_PASSWORD':
        return 'apiErrors.incorrectCredentials';
      case 'NAME_TAKEN':
        return 'apiErrors.takenName';
      case 'SHORTCUT_TAKEN':
        return 'apiErrors.takenShortcut';
      case 'WRONG_PASSWORD':
        return 'apiErrors.incorrectPassword';
      case 'EMAIL_TAKEN':
        return 'apiErrors.takenEmail';
      default:
        return 'apiErrors.unexpectedError';
    }
  }
}
