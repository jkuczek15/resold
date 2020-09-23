// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'delivery-quote-request.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

DeliveryQuoteRequest _$DeliveryQuoteRequestFromJson(Map<String, dynamic> json) {
  return DeliveryQuoteRequest(
    dropoff_address: json['dropoff_address'] as String,
    pickup_address: json['pickup_address'] as String,
    dropoff_deadline_dt: DateTime.parse(json['dropoff_deadline_dt'] as String),
    dropoff_latitude: (json['dropoff_latitude'] as num).toDouble(),
    dropoff_longitude: (json['dropoff_longitude'] as num).toDouble(),
    dropoff_phone_number: json['dropoff_phone_number'] as String,
    dropoff_ready_dt: DateTime.parse(json['dropoff_ready_dt'] as String),
    pickup_deadline_dt: DateTime.parse(json['pickup_deadline_dt'] as String),
    pickup_latitude: (json['pickup_latitude'] as num).toDouble(),
    pickup_longitude: (json['pickup_longitude'] as num).toDouble(),
    pickup_phone_number: json['pickup_phone_number'] as String,
    pickup_ready_dt: DateTime.parse(json['pickup_ready_dt'] as String),
  );
}

Map<String, dynamic> _$DeliveryQuoteRequestToJson(
        DeliveryQuoteRequest instance) =>
    <String, dynamic>{
      'dropoff_address': instance.dropoff_address,
      'pickup_address': instance.pickup_address,
      'dropoff_deadline_dt': instance.dropoff_deadline_dt.toIso8601String(),
      'dropoff_latitude': instance.dropoff_latitude,
      'dropoff_longitude': instance.dropoff_longitude,
      'dropoff_phone_number': instance.dropoff_phone_number,
      'dropoff_ready_dt': instance.dropoff_ready_dt.toIso8601String(),
      'pickup_deadline_dt': instance.pickup_deadline_dt.toIso8601String(),
      'pickup_latitude': instance.pickup_latitude,
      'pickup_longitude': instance.pickup_longitude,
      'pickup_phone_number': instance.pickup_phone_number,
      'pickup_ready_dt': instance.pickup_ready_dt.toIso8601String(),
    };
