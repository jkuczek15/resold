import 'package:rebloc/rebloc.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';

class UpdateCustomerAction extends Action {
  final CustomerResponse newCustomer;

  const UpdateCustomerAction(this.newCustomer);
}
