import 'package:rebloc/rebloc.dart';
import 'package:resold/state/actions/set-pill-position.dart';
import 'package:resold/state/actions/set-selected-product.dart';
import 'package:resold/state/app-state.dart';

class MapReducer extends SimpleBloc<AppState> {
  @override
  AppState reducer(AppState state, Action action) {
    if (action is SetPillPositionAction) {
      state.mapState.pillPosition = action.newPillPosition;
    } else if (action is SetSelectedProductAction) {
      state.mapState.selectedProduct = action.newProduct;
    }
    return state;
  }
}
