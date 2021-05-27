// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'delivery-response.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

DeliveryResponse _$DeliveryResponseFromJson(Map<String, dynamic> json) {
  return DeliveryResponse(
    id: json['id'] as String,
    fee: json['fee'] as int,
    kind: json['kind'] as String,
    live_mode: json['live_mode'] as bool,
    complete: json['complete'] as bool,
    created: json['created'] == null
        ? null
        : DateTime.parse(json['created'] as String),
    currency: json['currency'] as String,
    courier: json['courier'] == null
        ? null
        : CourierInfo.fromJson(json['courier'] as Map<String, dynamic>),
    courier_imminent: json['courier_imminent'] as bool,
    dropoff: json['dropoff'] == null
        ? null
        : WaypointInfo.fromJson(json['dropoff'] as Map<String, dynamic>),
    dropoff_deadline: json['dropoff_deadline'] == null
        ? null
        : DateTime.parse(json['dropoff_deadline'] as String),
    dropoff_eta: json['dropoff_eta'] == null
        ? null
        : DateTime.parse(json['dropoff_eta'] as String),
    dropoff_identifier: json['dropoff_identifier'] as String,
    dropoff_ready: json['dropoff_ready'] == null
        ? null
        : DateTime.parse(json['dropoff_ready'] as String),
    pickup: json['pickup'] == null
        ? null
        : WaypointInfo.fromJson(json['pickup'] as Map<String, dynamic>),
    pickup_deadline: json['pickup_deadline'] == null
        ? null
        : DateTime.parse(json['pickup_deadline'] as String),
    pickup_eta: json['pickup_eta'] == null
        ? null
        : DateTime.parse(json['pickup_eta'] as String),
    pickup_ready: json['pickup_ready'] == null
        ? null
        : DateTime.parse(json['pickup_ready'] as String),
    manifest: json['manifest'] == null
        ? null
        : ManifestInfo.fromJson(json['manifest'] as Map<String, dynamic>),
    manifest_items: (json['manifest_items'] as List)
        ?.map((e) =>
            e == null ? null : ManifestItem.fromJson(e as Map<String, dynamic>))
        ?.toList(),
    quote_id: json['quote_id'] as String,
    status: json['status'] as String,
    tip: json['tip'] as int,
    tracking_url: json['tracking_url'] as String,
    undeliverable_action: json['undeliverable_action'] as String,
    undeliverable_reason: json['undeliverable_reason'] as String,
    uuid: json['uuid'] as String,
    statusCode: json['statusCode'],
    error: json['error'],
  );
}

Map<String, dynamic> _$DeliveryResponseToJson(DeliveryResponse instance) =>
    <String, dynamic>{
      'statusCode': instance.statusCode,
      'error': instance.error,
      'id': instance.id,
      'fee': instance.fee,
      'kind': instance.kind,
      'live_mode': instance.live_mode,
      'complete': instance.complete,
      'created': instance.created?.toIso8601String(),
      'currency': instance.currency,
      'courier': instance.courier,
      'courier_imminent': instance.courier_imminent,
      'dropoff': instance.dropoff,
      'dropoff_deadline': instance.dropoff_deadline?.toIso8601String(),
      'dropoff_eta': instance.dropoff_eta?.toIso8601String(),
      'dropoff_identifier': instance.dropoff_identifier,
      'dropoff_ready': instance.dropoff_ready?.toIso8601String(),
      'pickup': instance.pickup,
      'pickup_deadline': instance.pickup_deadline?.toIso8601String(),
      'pickup_eta': instance.pickup_eta?.toIso8601String(),
      'pickup_ready': instance.pickup_ready?.toIso8601String(),
      'manifest': instance.manifest,
      'manifest_items': instance.manifest_items,
      'quote_id': instance.quote_id,
      'status': instance.status,
      'tip': instance.tip,
      'tracking_url': instance.tracking_url,
      'undeliverable_action': instance.undeliverable_action,
      'undeliverable_reason': instance.undeliverable_reason,
      'uuid': instance.uuid,
    };

CourierInfo _$CourierInfoFromJson(Map<String, dynamic> json) {
  return CourierInfo(
    name: json['name'] as String,
    rating: (json['rating'] as num)?.toDouble(),
    vehicle_type: json['vehicle_type'] as String,
    phone_number: json['phone_number'] as String,
    location: json['location'] == null
        ? null
        : LatLng.fromJson(json['location'] as Map<String, dynamic>),
    img_href: json['img_href'] as String,
  );
}

Map<String, dynamic> _$CourierInfoToJson(CourierInfo instance) =>
    <String, dynamic>{
      'name': instance.name,
      'rating': instance.rating,
      'vehicle_type': instance.vehicle_type,
      'phone_number': instance.phone_number,
      'location': instance.location,
      'img_href': instance.img_href,
    };

WaypointInfo _$WaypointInfoFromJson(Map<String, dynamic> json) {
  return WaypointInfo(
    name: json['name'] as String,
    phone_number: json['phone_number'] as String,
    address: json['address'] as String,
    detailed_address: json['detailed_address'] == null
        ? null
        : Address.fromJson(json['detailed_address'] as Map<String, dynamic>),
    notes: json['notes'] as String,
    location: json['location'] == null
        ? null
        : LatLng.fromJson(json['location'] as Map<String, dynamic>),
  );
}

Map<String, dynamic> _$WaypointInfoToJson(WaypointInfo instance) =>
    <String, dynamic>{
      'name': instance.name,
      'phone_number': instance.phone_number,
      'address': instance.address,
      'detailed_address': instance.detailed_address,
      'notes': instance.notes,
      'location': instance.location,
    };

ManifestInfo _$ManifestInfoFromJson(Map<String, dynamic> json) {
  return ManifestInfo(
    reference: json['reference'] as String,
    description: json['description'] as String,
  );
}

Map<String, dynamic> _$ManifestInfoToJson(ManifestInfo instance) =>
    <String, dynamic>{
      'reference': instance.reference,
      'description': instance.description,
    };

Address _$AddressFromJson(Map<String, dynamic> json) {
  return Address(
    street_address_1: json['street_address_1'] as String,
    street_address_2: json['street_address_2'] as String,
    city: json['city'] as String,
    state: json['state'] as String,
    zip_code: json['zip_code'] as String,
    country: json['country'] as String,
    sublocality_level_1: json['sublocality_level_1'] as String,
  );
}

Map<String, dynamic> _$AddressToJson(Address instance) => <String, dynamic>{
      'street_address_1': instance.street_address_1,
      'street_address_2': instance.street_address_2,
      'city': instance.city,
      'state': instance.state,
      'zip_code': instance.zip_code,
      'country': instance.country,
      'sublocality_level_1': instance.sublocality_level_1,
    };

Verification _$VerificationFromJson(Map<String, dynamic> json) {
  return Verification(
    verification: json['verification'] == null
        ? null
        : SignatureVerification.fromJson(
            json['verification'] as Map<String, dynamic>),
  );
}

Map<String, dynamic> _$VerificationToJson(Verification instance) =>
    <String, dynamic>{
      'verification': instance.verification,
    };

SignatureVerification _$SignatureVerificationFromJson(
    Map<String, dynamic> json) {
  return SignatureVerification(
    image_url: json['image_url'] as String,
  );
}

Map<String, dynamic> _$SignatureVerificationToJson(
        SignatureVerification instance) =>
    <String, dynamic>{
      'image_url': instance.image_url,
    };

LatLng _$LatLngFromJson(Map<String, dynamic> json) {
  return LatLng(
    lat: (json['lat'] as num)?.toDouble(),
    lng: (json['lng'] as num)?.toDouble(),
  );
}

Map<String, dynamic> _$LatLngToJson(LatLng instance) => <String, dynamic>{
      'lat': instance.lat,
      'lng': instance.lng,
    };
