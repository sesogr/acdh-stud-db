export type IdRecord = { person_id: number };
export type Property =
  | "biography"
  | "birth_date"
  | "birth_place"
  | "ethnicity"
  | "father"
  | "gender"
  | "given_names"
  | "graduation"
  | "guardian"
  | "language"
  | "last_name"
  | "last_school"
  | "literature"
  | "nationality"
  | "religion"
  | "remarks"
  | "studying_address";
export type PropRecord = IdRecord & {
  property: Property;
  id: number;
  value: string;
  value2: string;
  value3: string;
  is_doubtful: "" | "true";
  times: string;
  year_min: number;
  year_max: number;
};
export type Person = IdRecord & Partial<Record<Property, string[]>>;
type Mean = number;
type Median = number;
type Minimum = number;
type Maximum = number;
type Count = number;
export type Stats = [Mean, Median, Minimum, Maximum, Count];
export type Comparison = {
  idLow: number;
  idHigh: number;
  stats: Record<Property, Stats>;
};
