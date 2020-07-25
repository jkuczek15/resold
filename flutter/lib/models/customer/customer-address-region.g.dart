// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'customer-address-region.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

CustomerAddressRegion _$CustomerAddressRegionFromJson(
    Map<String, dynamic> json) {
  return CustomerAddressRegion(
    regionId: json['regionId'] as int,
    regionCode: json['regionCode'] as String,
    region: json['region'] as String,
  );
}

Map<String, dynamic> _$CustomerAddressRegionToJson(
        CustomerAddressRegion instance) =>
    <String, dynamic>{
      'regionId': instance.regionId,
      'regionCode': instance.regionCode,
      'region': instance.region,
    };
