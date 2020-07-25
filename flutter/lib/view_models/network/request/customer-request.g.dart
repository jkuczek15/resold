// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'customer-request.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

CustomerRequest _$CustomerRequestFromJson(Map<String, dynamic> json) {
  return CustomerRequest(
    email: json['email'] as String,
    firstname: json['firstname'] as String,
    lastname: json['lastname'] as String,
    addresses: (json['addresses'] as List)
        .map((e) => CustomerAddress.fromJson(e as Map<String, dynamic>))
        .toList(),
  );
}

Map<String, dynamic> _$CustomerRequestToJson(CustomerRequest instance) =>
    <String, dynamic>{
      'email': instance.email,
      'firstname': instance.firstname,
      'lastname': instance.lastname,
      'addresses': instance.addresses,
    };
