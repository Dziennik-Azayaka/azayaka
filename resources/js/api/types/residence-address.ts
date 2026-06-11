export interface ResidenceAddress {
  id: number;
  country: string;
  commune: string | null;
  town: string | null;
  postalCode: string | null;
  street: string | null;
  houseNumber: string | null;
  flatNumber: string | null;
}
