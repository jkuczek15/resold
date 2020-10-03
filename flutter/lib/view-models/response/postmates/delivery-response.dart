import 'package:resold/view-models/request/postmates/delivery-request.dart';
import 'package:resold/view-models/response/abstract-response.dart';
import 'package:json_annotation/json_annotation.dart';

part 'delivery-response.g.dart';

@JsonSerializable(nullable: true)
class DeliveryResponse extends Response {

  // general info
  final String id;                  // delivery ID
  final int fee;                    // delivery fee
  final String kind;                // kind of object (delivery)
  final bool live_mode;             // indicates live mode or test mode
  final bool complete;              // flag indicating if delivery is ongoing
  final DateTime created;           // timestamp when delivery was created
  final String currency;            // three-letter ISO currency code

  // courier info
  final CourierInfo courier;        // person delivering the item, includes current location
  final bool courier_imminent;      // flag indicating if courier is close to dropoff

  // dropoff info
  final WaypointInfo dropoff;       // dropoff location
  final DateTime dropoff_deadline;  // deadline for dropoff
  final DateTime dropoff_eta;       // estimated dropoff time
  final String dropoff_identifier;  // who received delivery at dropoff
  final DateTime dropoff_ready;     // start of the dropoff window

  // pickup info
  final WaypointInfo pickup;       // dropoff location
  final DateTime pickup_deadline;  // deadline for dropoff
  final DateTime pickup_eta;       // estimated dropoff time
  final DateTime pickup_ready;     // start of the dropoff window

  // manifest
  final ManifestInfo manifest;                // info about what is being delivered
  final List<ManifestItem> manifest_items;   // list of items driver will be delivering

  // status info
  final String quote_id;            // delivery quote id if provided
  final String status;              // delivery status, see: https://postmates.com/developer/docs/#resources__delivery__create-delivery
  final int tip;                    // tip given to driver in cents

  // tracking and deliverability
  final String tracking_url;
  final String undeliverable_action;
  final String undeliverable_reason;
  final String uuid;                // alternative delivery ID

  DeliveryResponse({this.id, this.fee, this.kind, this.live_mode, this.complete, this.created, this.currency, this.courier, this.courier_imminent,
  this.dropoff, this.dropoff_deadline, this.dropoff_eta, this.dropoff_identifier, this.dropoff_ready, this.pickup, this.pickup_deadline, this.pickup_eta,
  this.pickup_ready, this.manifest, this.manifest_items, this.quote_id, this.status, this.tip, this.tracking_url, this.undeliverable_action, this.undeliverable_reason,
  this.uuid, statusCode, error}) : super(statusCode: statusCode, error: error);

  factory DeliveryResponse.fromJson(Map<String, dynamic> json) => _$DeliveryResponseFromJson(json);
  Map<String, dynamic> toJson() => _$DeliveryResponseToJson(this);
}

@JsonSerializable(nullable: true)
class CourierInfo {
  final String name;            // courier's first name
  final double rating;           // courier's rating
  final String vehicle_type;     // bicycle, car, van, truck, scooter, motorcycle, walker
  final String phone_number;     // courier's phone number
  final LatLng location;        // courier's current location
  final String img_href;        // courier's profile image

  CourierInfo({this.name, this.rating, this.vehicle_type, this.phone_number, this.location, this.img_href});

  factory CourierInfo.fromJson(Map<String, dynamic> json) => _$CourierInfoFromJson(json);
  Map<String, dynamic> toJson() => _$CourierInfoToJson(this);
}

@JsonSerializable(nullable: true)
class WaypointInfo {
  final String name;                // name of person at this waypoint
  final String phone_number;        // phone number of the waypoint
  final String address;             // address of the waypoint
  final Address detailed_address;   // detailed waypoint address
  final String notes;               // additional waypoint notes
  final LatLng location;            // latitude/longitude associated with waypoint

  WaypointInfo({this.name, this.phone_number, this.address, this.detailed_address, this.notes, this.location});

  factory WaypointInfo.fromJson(Map<String, dynamic> json) => _$WaypointInfoFromJson(json);
  Map<String, dynamic> toJson() => _$WaypointInfoToJson(this);
}

@JsonSerializable(nullable: true)
class ManifestInfo {
  final String reference;                    // reference that identifies the manfiest
  final String description;                  // description of what the driver will be delivering

  ManifestInfo({this.reference, this.description});

  factory ManifestInfo.fromJson(Map<String, dynamic> json) => _$ManifestInfoFromJson(json);
  Map<String, dynamic> toJson() => _$ManifestInfoToJson(this);
}

@JsonSerializable(nullable: true)
class Address {
  final String street_address_1;
  final String street_address_2;
  final String city;
  final String state;
  final String zip_code;
  final String country;
  final String sublocality_level_1;

  Address({this.street_address_1, this.street_address_2, this.city, this.state, this.zip_code, this.country, this.sublocality_level_1});

  factory Address.fromJson(Map<String, dynamic> json) => _$AddressFromJson(json);
  Map<String, dynamic> toJson() => _$AddressToJson(this);
}

@JsonSerializable(nullable: true)
class Verification {
  final SignatureVerification verification;

  Verification({this.verification});

  factory Verification.fromJson(Map<String, dynamic> json) => _$VerificationFromJson(json);
  Map<String, dynamic> toJson() => _$VerificationToJson(this);
}

@JsonSerializable(nullable: true)
class SignatureVerification {
  final String image_url;

  SignatureVerification({this.image_url});

  factory SignatureVerification.fromJson(Map<String, dynamic> json) => _$SignatureVerificationFromJson(json);
  Map<String, dynamic> toJson() => _$SignatureVerificationToJson(this);
}

@JsonSerializable(nullable: true)
class LatLng {
  final double lat;
  final double lng;

  LatLng({this.lat, this.lng});

  factory LatLng.fromJson(Map<String, dynamic> json) => _$LatLngFromJson(json);
  Map<String, dynamic> toJson() => _$LatLngToJson(this);
}
