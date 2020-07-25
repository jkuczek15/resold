
class Customer {
  final String email;
  final String firstName;
  final String lastName;
  final List<CustomerAddress> addresses;
  final String password;

  Customer({this.email, this.firstName, this.lastName, this.addresses, this.password});
}

class CustomerAddress {
  bool defaultBilling = true;
  bool defaultShipping = true;

  String firstName;
  String lastName;

  CustomerAddressRegion region;
  List<String> street;

  String postCode;
  String city;
  String telephone;
  String countryId;

  CustomerAddress({this.defaultBilling, this.defaultShipping, this.firstName, this.lastName, this.region,
    this.street, this.postCode, this.city, this.telephone, this.countryId});
}

class CustomerAddressRegion {
  int regionId;
  String regionCode;
  String region;

  CustomerAddressRegion({this.regionId, this.regionCode, this.region});
}