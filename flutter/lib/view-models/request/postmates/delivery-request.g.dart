// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'delivery-request.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

DeliveryRequest _$DeliveryRequestFromJson(Map<String, dynamic> json) {
  return DeliveryRequest(
    dropoff_address: json['dropoff_address'] as String,
    pickup_address: json['pickup_address'] as String,
    dropoff_deadline_dt: json['dropoff_deadline_dt'] as String,
    dropoff_latitude: (json['dropoff_latitude'] as num)?.toDouble(),
    dropoff_longitude: (json['dropoff_longitude'] as num)?.toDouble(),
    dropoff_phone_number: json['dropoff_phone_number'] as String,
    dropoff_ready_dt: json['dropoff_ready_dt'] as String,
    pickup_deadline_dt: json['pickup_deadline_dt'] as String,
    pickup_latitude: (json['pickup_latitude'] as num)?.toDouble(),
    pickup_longitude: (json['pickup_longitude'] as num)?.toDouble(),
    pickup_phone_number: json['pickup_phone_number'] as String,
    pickup_ready_dt: json['pickup_ready_dt'] as String,
    manifest: json['manifest'] as String,
    manifest_items: (json['manifest_items'] as List)
        ?.map((e) =>
            e == null ? null : ManifestItem.fromJson(e as Map<String, dynamic>))
        ?.toList(),
    dropoff_notes: json['dropoff_notes'] as String,
    dropoff_name: json['dropoff_name'] as String,
    pickup_name: json['pickup_name'] as String,
  );
}

Map<String, dynamic> _$DeliveryRequestToJson(DeliveryRequest instance) =>
    <String, dynamic>{
      'dropoff_name': instance.dropoff_name,
      'dropoff_address': instance.dropoff_address,
      'dropoff_deadline_dt': instance.dropoff_deadline_dt,
      'dropoff_latitude': instance.dropoff_latitude,
      'dropoff_longitude': instance.dropoff_longitude,
      'dropoff_phone_number': instance.dropoff_phone_number,
      'dropoff_ready_dt': instance.dropoff_ready_dt,
      'dropoff_notes': instance.dropoff_notes,
      'pickup_name': instance.pickup_name,
      'pickup_address': instance.pickup_address,
      'pickup_deadline_dt': instance.pickup_deadline_dt,
      'pickup_latitude': instance.pickup_latitude,
      'pickup_longitude': instance.pickup_longitude,
      'pickup_phone_number': instance.pickup_phone_number,
      'pickup_ready_dt': instance.pickup_ready_dt,
      'manifest': instance.manifest,
      'manifest_items': instance.manifest_items,
    };

ManifestItem _$ManifestItemFromJson(Map<String, dynamic> json) {
  return ManifestItem(
    name: json['name'] as String,
    quantity: json['quantity'] as int,
    size: json['size'] as String,
  );
}

Map<String, dynamic> _$ManifestItemToJson(ManifestItem instance) =>
    <String, dynamic>{
      'name': instance.name,
      'quantity': instance.quantity,
      'size': instance.size,
    };
