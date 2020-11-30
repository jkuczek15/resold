import 'package:geolocator/geolocator.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';

class TestAccounts {
  static CustomerResponse joe = CustomerResponse(email: 'joe.kuczek@gmail.com', password: 'Resold420!');
  static CustomerResponse jim = CustomerResponse(email: 'jim.smith@gmail.com', password: 'Resold420!');
  static CustomerResponse bob = CustomerResponse(email: 'bob.smith@gmail.com', password: 'Resold420!');
}

class TestLocations {
  static Position evanston = Position(latitude: 42.052158, longitude: -87.687866);
}
