import 'package:resold/models/customer/customer-address.dart';
import 'package:json_annotation/json_annotation.dart';

part 'customer-request.g.dart';

@JsonSerializable(nullable: false)
class CustomerRequest {
  final String email;
  final String firstname;
  final String lastname;
  final List<CustomerAddress> addresses;

  CustomerRequest({this.email, this.firstname, this.lastname, this.addresses});

  factory CustomerRequest.fromJson(Map<String, dynamic> json) => _$CustomerRequestFromJson(json);
  Map<String, dynamic> toJson() => _$CustomerRequestToJson(this);
}
