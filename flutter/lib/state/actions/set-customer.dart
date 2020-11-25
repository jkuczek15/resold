import 'package:rebloc/rebloc.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';

class SetCustomerAction extends Action {
  final CustomerResponse newCustomer;

  const SetCustomerAction(this.newCustomer);
}
