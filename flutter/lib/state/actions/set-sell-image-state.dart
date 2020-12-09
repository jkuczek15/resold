import 'package:rebloc/rebloc.dart';
import 'package:resold/state/screens/sell/sell-image-state.dart';

class SetSellImageStateAction extends Action {
  final SellImageState newState;

  const SetSellImageStateAction(this.newState);
}
