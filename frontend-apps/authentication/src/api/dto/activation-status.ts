export type ActivationStatus =
    | {
          step: "notStarted";
      }
    | {
          step: "code_found";
          code: string;
      }
    | {
          step: "email_available" | "attach_to_account";
          code: string;
          email: string;
      };
