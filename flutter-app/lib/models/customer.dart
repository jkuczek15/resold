
class Customer {
  final String email;
  final String firstName;
  final String lastName;
  final List<CustomerAddress> addresses;
  final String password;

  Customer({this.email, this.firstName, this.lastName, this.addresses, this.password});
}

class CustomerAddress {
  final bool defaultBilling = true;
  final bool defaultShipping = true;

  String firstName;
  String lastName;

  CustomerAddressRegion region;
  List<String> street;

  String postCode;
  String city;
  String telephone;
  String countryId;
}

class CustomerAddressRegion {
  int regionId;
  String regionCode;
  String region;
}