import 'package:rebloc/rebloc.dart';
import 'package:resold/enums/selected-tab.dart';

class SetSelectedTabAction extends Action {
  final SelectedTab selectedTab;

  const SetSelectedTabAction(this.selectedTab);
}
