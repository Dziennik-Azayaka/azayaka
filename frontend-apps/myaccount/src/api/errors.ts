export class ApiError extends Error {
    constructor(message: string) {
        super(message);
        this.name = "ApiError";
    }
}

export class IncorrectPasswordError extends Error {
	constructor() {
        super();
        this.name = "IncorrectPasswordError";
    }
}

export class AlreadyTakenEmailAddressError extends Error {
	constructor() {
		super();
		this.name = "AlreadyTakenEmailAddressError"
	}
}
