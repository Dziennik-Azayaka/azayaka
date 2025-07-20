export class ApiError extends Error {
    constructor(message: string) {
        super(message);
        this.name = "ApiError";
    }
}

export class IncorrectCredentialsError extends Error {
    constructor() {
        super();
        this.name = "IncorrectCredentialsError";
    }
}

export class IncorrectActivationCodeError extends Error {
    constructor() {
        super();
        this.name = "IncorrectActivationCodeError";
    }
}
