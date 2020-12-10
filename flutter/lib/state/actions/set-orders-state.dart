import 'package:rebloc/rebloc.dart';
import 'package:resold/state/screens/orders-state.dart';

class SetOrdersStateAction extends Action {
  final OrdersState newState;

  const SetOrdersStateAction(this.newState);
}
