export type SessionInfoDTO =
    | {
          loggedIn: true;
          email: string;
          name: string;
      }
    | {
          loggedIn: false;
      };
