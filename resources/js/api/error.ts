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
      case 'ACTIVATION_CODE_NOT_FOUND':
        return 'apiErrors.incorrectCode';
      case 'INVALID_CREDENTIALS':
        return 'apiErrors.incorrectCredentials';
      case 'NAME_TAKEN':
        return 'apiErrors.takenName';
      case 'SHORTCUT_TAKEN':
        return 'apiErrors.takenShortcut';
      case 'WRONG_PASSWORD':
        return 'apiErrors.incorrectPassword';
      case 'STUDENT_NOT_IN_ACTIVE_CLASS_UNIT':
        return 'apiErrors.studentNotInActiveClassUnit';
      case 'EMAIL_TAKEN':
        return 'apiErrors.takenEmail';
      case 'ENTITY_HAS_NO_ACCESS_WORDS':
        return 'apiErrors.entityHasNoAccessWords';
      case 'GRADEBOOK_ALREADY_EXISTS':
        return 'apiErrors.gradebookExists';
      case 'SCHOOL_UNIT_NOT_ACTIVE':
        return 'apiErrors.schoolUnitNotActive';
      default:
        return 'apiErrors.unexpectedError';
    }
  }
}
