// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'customer-address.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

CustomerAddress _$CustomerAddressFromJson(Map<String, dynamic> json) {
  return CustomerAddress(
    defaultBilling: json['defaultBilling'] as bool,
    defaultShipping: json['defaultShipping'] as bool,
    firstname: json['firstname'] as String,
    lastname: json['lastname'] as String,
    region: json['region'] == null
        ? null
        : CustomerAddressRegion.fromJson(
            json['region'] as Map<String, dynamic>),
    street: (json['street'] as List)?.map((e) => e as String)?.toList(),
    postcode: json['postcode'] as String,
    city: json['city'] as String,
    countryId: json['countryId'] as String,
  );
}

Map<String, dynamic> _$CustomerAddressToJson(CustomerAddress instance) =>
    <String, dynamic>{
      'defaultBilling': instance.defaultBilling,
      'defaultShipping': instance.defaultShipping,
      'firstname': instance.firstname,
      'lastname': instance.lastname,
      'region': instance.region,
      'street': instance.street,
      'postcode': instance.postcode,
      'city': instance.city,
      'countryId': instance.countryId,
    };
