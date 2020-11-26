import 'package:rebloc/rebloc.dart';

class SetPillPositionAction extends Action {
  final double newPillPosition;

  const SetPillPositionAction(this.newPillPosition);
}
