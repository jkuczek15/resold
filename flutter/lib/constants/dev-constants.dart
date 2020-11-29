import 'package:geolocator/geolocator.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';

Map<String, CustomerResponse> testAccounts = {
  'Joe': CustomerResponse(email: 'joe.kuczek@gmail.com', password: 'Resold420!'),
  'Jim': CustomerResponse(email: 'jim.smith@gmail.com', password: 'Resold420!'),
  'Bob': CustomerResponse(email: 'bob.smith@gmail.com', password: 'Resold420!')
};

Map<String, Position> testLocations = {'Evanston': Position(latitude: 42.052158, longitude: -87.687866)};
