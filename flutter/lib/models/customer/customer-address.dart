import 'package:json_annotation/json_annotation.dart';
import 'package:geocoder/geocoder.dart';
import 'customer-address-region.dart';

part 'customer-address.g.dart';

@JsonSerializable(nullable: true)
class CustomerAddress {
  bool defaultBilling;
  bool defaultShipping;

  String firstname;
  String lastname;

  CustomerAddressRegion region;
  List<String> street;

  String postcode;
  String city;
//  String telephone;  todo: get the user's phone number
  String countryId;

  CustomerAddress({this.defaultBilling, this.defaultShipping, this.firstname, this.lastname, this.region,
    this.street, this.postcode, this.city, this.countryId});

  factory CustomerAddress.fromAddress(Address address, String firstName, String lastName) {

    var customerAddress = CustomerAddress();
    var customerAddressRegion = CustomerAddressRegion();

    customerAddress.firstname = firstName;
    customerAddress.lastname = lastName;
    customerAddress.countryId = address.countryCode;
    customerAddress.postcode = address.postalCode;
    customerAddress.city = address.locality;
    customerAddress.street = [address.featureName + ' ' + address.thoroughfare];
    customerAddress.defaultShipping = true;
    customerAddress.defaultBilling = true;

    var addressParts = address.addressLine.split(',');

    if(addressParts.length > 1) {
      // we have a full address from geo-coding
      customerAddress.street = [addressParts[0]];
      customerAddress.city = addressParts[1];

      if(addressParts.length > 2) {
        // we have a state and postal code
        var stateParts = addressParts[2].trim().split(' ');

        if(stateParts.length > 1) {
          customerAddressRegion.regionCode = stateParts[0];
          customerAddressRegion.region = address.adminArea;
          customerAddress.postcode = stateParts[1];
        }// end if we have a state part in the address
      }// end if we have state and postal code
    }// end if we have a full address line

    customerAddress.region = customerAddressRegion;

    return customerAddress;
  }

  factory CustomerAddress.fromMap(List<dynamic> addresses) {
    var customerAddress = CustomerAddress();
    var customerAddressRegion = CustomerAddressRegion();

    if(addresses.length > 0) {
      var address = addresses[0];
      customerAddress.defaultBilling = true;
      customerAddress.defaultShipping = true;
      customerAddress.firstname = address['firstname'];
      customerAddress.lastname = address['lastname'];
      customerAddress.postcode = address['postcode'];
      customerAddress.city = address['city'];
      customerAddress.countryId = address['country_id'];
      customerAddress.street = [address['street'][0].toString()];

      if(address['region'] != null) {
        var region = address['region'];
        customerAddressRegion.region = region['region'];
        customerAddressRegion.regionCode = region['region_code'];
        customerAddressRegion.regionId = region['region_id'];
      }
    }

    customerAddress.region = customerAddressRegion;
    return customerAddress;
  }

  factory CustomerAddress.fromJson(Map<String, dynamic> json) => _$CustomerAddressFromJson(json);
  Map<String, dynamic> toJson() => _$CustomerAddressToJson(this);
}
