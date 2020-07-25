import 'package:json_annotation/json_annotation.dart';

part 'customer-address-region.g.dart';

@JsonSerializable(nullable: true)
class CustomerAddressRegion {
  int regionId;
  String regionCode;
  String region;

  CustomerAddressRegion({this.regionId, this.regionCode, this.region});

  factory CustomerAddressRegion.fromJson(Map<String, dynamic> json) => _$CustomerAddressRegionFromJson(json);
  Map<String, dynamic> toJson() => _$CustomerAddressRegionToJson(this);
}
