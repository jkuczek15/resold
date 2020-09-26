import 'package:json_annotation/json_annotation.dart';

part 'delivery-quote-request.g.dart';

@JsonSerializable(nullable: true)
class DeliveryQuoteRequest {

  // dropoff info
  final String dropoff_address;
  final String dropoff_deadline_dt;
  final double dropoff_latitude;
  final double dropoff_longitude;
  final String dropoff_phone_number;
  final String dropoff_ready_dt;

  // pickup info
  final String pickup_address;
  final String pickup_deadline_dt;
  final double pickup_latitude;
  final double pickup_longitude;
  final String pickup_phone_number;
  final String pickup_ready_dt;

  DeliveryQuoteRequest({this.dropoff_address, this.pickup_address, this.dropoff_deadline_dt, this.dropoff_latitude, this.dropoff_longitude,
  this.dropoff_phone_number, this.dropoff_ready_dt, this.pickup_deadline_dt, this.pickup_latitude, this.pickup_longitude, this.pickup_phone_number, this.pickup_ready_dt});

  factory DeliveryQuoteRequest.fromJson(Map<String, dynamic> json) => _$DeliveryQuoteRequestFromJson(json);
  Map<String, dynamic> toJson() => _$DeliveryQuoteRequestToJson(this);
}
