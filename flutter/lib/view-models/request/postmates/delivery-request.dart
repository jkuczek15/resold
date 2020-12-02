import 'package:json_annotation/json_annotation.dart';

part 'delivery-request.g.dart';

// ignore_for_file: non_constant_identifier_names
@JsonSerializable(nullable: true, explicitToJson: true)
class DeliveryRequest {
  // delivery defaults
  final String deliverable_action = 'deliverable_action_leave_at_door';
  final String undeliverable_action = 'leave_at_door';
  final bool requires_dropoff_signature = false;
  final bool requires_id = false;

  // dropoff info
  final String dropoff_name;
  final String dropoff_address;
  final String dropoff_deadline_dt;
  final double dropoff_latitude;
  final double dropoff_longitude;
  final String dropoff_phone_number;
  final String dropoff_ready_dt;
  final String dropoff_notes;

  // pickup info
  final String pickup_name;
  final String pickup_address;
  final String pickup_deadline_dt;
  final double pickup_latitude;
  final double pickup_longitude;
  final String pickup_phone_number;
  final String pickup_ready_dt;

  // robot info
  String robo_delivered;
  String robo_dropoff;
  String robo_pickup;
  String robo_pickup_complete;
  String robo_undeliverable_action;

  // manifest, what the courier will be delivering
  final String manifest;
  final String manifest_reference;
  final List<ManifestItem> manifest_items;

  DeliveryRequest(
      {this.dropoff_address,
      this.pickup_address,
      this.dropoff_deadline_dt,
      this.dropoff_latitude,
      this.dropoff_longitude,
      this.dropoff_phone_number,
      this.dropoff_ready_dt,
      this.pickup_deadline_dt,
      this.pickup_latitude,
      this.pickup_longitude,
      this.pickup_phone_number,
      this.pickup_ready_dt,
      this.manifest,
      this.manifest_reference,
      this.manifest_items,
      this.dropoff_notes,
      this.dropoff_name,
      this.pickup_name,
      this.robo_delivered,
      this.robo_dropoff,
      this.robo_pickup,
      this.robo_pickup_complete,
      this.robo_undeliverable_action});

  factory DeliveryRequest.fromJson(Map<String, dynamic> json) => _$DeliveryRequestFromJson(json);
  Map<String, dynamic> toJson() => _$DeliveryRequestToJson(this);
}

@JsonSerializable(nullable: true)
class ManifestItem {
  final String name;
  final int quantity;
  final String size; // small, medium, large, xlarge

  ManifestItem({this.name, this.quantity, this.size});

  factory ManifestItem.fromJson(Map<String, dynamic> json) => _$ManifestItemFromJson(json);
  Map<String, dynamic> toJson() => _$ManifestItemToJson(this);
}
