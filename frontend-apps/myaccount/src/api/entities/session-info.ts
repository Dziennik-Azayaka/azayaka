export type SessionInfoEntity =
    | {
          loggedIn: true;
          emailAddress: string;
      }
    | {
          loggedIn: false;
      };
