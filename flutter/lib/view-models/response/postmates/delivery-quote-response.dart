import 'package:resold/view-models/response/abstract-response.dart';
import 'package:json_annotation/json_annotation.dart';

part 'delivery-quote-response.g.dart';

@JsonSerializable(nullable: true)
class DeliveryQuoteResponse extends Response {

  final String id;                   // delivery quote id
  final DateTime created;
  final String currency;
  final String currency_type;
  final DateTime dropoff_eta;
  final int duration;            // estimated minutes for delivery to reach drop-off
  final DateTime expires;
  final int fee;                // amount in cents that will be charged if delivery is created
  final String kind;
  final int pickup_duration;    // estimated minutes until a courier will arrive at the pickup

  DeliveryQuoteResponse({this.id, this.created, this.currency, this.currency_type, this.dropoff_eta, this.duration, this.expires, this.fee, this.kind, this.pickup_duration,
    statusCode, error}) : super(statusCode: statusCode, error: error);

  factory DeliveryQuoteResponse.fromJson(Map<String, dynamic> json) => _$DeliveryQuoteResponseFromJson(json);
  Map<String, dynamic> toJson() => _$DeliveryQuoteResponseToJson(this);
}
