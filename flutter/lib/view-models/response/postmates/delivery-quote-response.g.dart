// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'delivery-quote-response.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

DeliveryQuoteResponse _$DeliveryQuoteResponseFromJson(
    Map<String, dynamic> json) {
  return DeliveryQuoteResponse(
    id: json['id'] as String,
    created: json['created'] == null
        ? null
        : DateTime.parse(json['created'] as String),
    currency: json['currency'] as String,
    currency_type: json['currency_type'] as String,
    dropoff_eta: json['dropoff_eta'] == null
        ? null
        : DateTime.parse(json['dropoff_eta'] as String),
    duration: json['duration'] as int,
    expires: json['expires'] == null
        ? null
        : DateTime.parse(json['expires'] as String),
    fee: json['fee'] as int,
    kind: json['kind'] as String,
    pickup_duration: json['pickup_duration'] as int,
    status: json['status'],
    error: json['error'],
  );
}

Map<String, dynamic> _$DeliveryQuoteResponseToJson(
        DeliveryQuoteResponse instance) =>
    <String, dynamic>{
      'status': instance.status,
      'error': instance.error,
      'id': instance.id,
      'created': instance.created?.toIso8601String(),
      'currency': instance.currency,
      'currency_type': instance.currency_type,
      'dropoff_eta': instance.dropoff_eta?.toIso8601String(),
      'duration': instance.duration,
      'expires': instance.expires?.toIso8601String(),
      'fee': instance.fee,
      'kind': instance.kind,
      'pickup_duration': instance.pickup_duration,
    };
