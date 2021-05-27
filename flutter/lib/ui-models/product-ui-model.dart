import 'package:flutter/material.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/models/product.dart';
import 'package:resold/services/search.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/state/screens/search-state.dart';

class ProductUiModel extends ChangeNotifier {
  final int itemRequestThreshold = 20;
  List<Product> items;
  int lastLoadingIndex = 0;
  Position currentLocation;
  SearchState searchState;

  ProductUiModel({this.currentLocation, this.searchState, this.items});

  Future handleItemCreated(int index) async {
    int itemPosition = index + 1;
    bool requestMoreData = itemPosition % itemRequestThreshold == 0 && itemPosition != 0;
    int pageToRequest = itemPosition ~/ itemRequestThreshold;

    if (requestMoreData && pageToRequest > searchState.currentPage) {
      searchState.currentPage = pageToRequest;
      showLoadingIndicator();

      List<Product> newItems = await Search.fetchSearchProducts(
          searchState, currentLocation.latitude, currentLocation.longitude,
          offset: pageToRequest * itemRequestThreshold);
      items.addAll(newItems);

      removeLoadingIndicator();
    } // end if requesting more data
  } // end function handleItemCreated

  void showLoadingIndicator() {
    items.add(Product(name: LoadingIndicatorTitle));
    lastLoadingIndex = items.length - 1;
    notifyListeners();
  } // end function showLoadingIndicator

  void removeLoadingIndicator() {
    items.removeAt(lastLoadingIndex);
    notifyListeners();
  } // end function removeLoadingIndicator
}
