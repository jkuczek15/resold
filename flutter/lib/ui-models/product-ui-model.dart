import 'package:flutter/material.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/models/product.dart';
import 'package:resold/services/search.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/state/search-state.dart';

class ProductUiModel extends ChangeNotifier {
  int itemRequestThreshold = 20;
  static int currentPage = 0;
  List<Product> items;
  int lastLoadingIndex = 0;
  Position currentLocation;
  SearchState searchState;

  ProductUiModel(Position currentLocation, SearchState searchState, List<Product> data) {
    items = data;
    searchState = searchState;
    this.currentLocation = currentLocation;
  }

  Future handleItemCreated(int index) async {
    itemRequestThreshold = this.items.length;

    var itemPosition = index + 1;
    var tempRequestThreshold = itemRequestThreshold - 1;
    var requestMoreData = itemPosition % tempRequestThreshold == 0 && itemPosition != 0;
    var pageToRequest = itemPosition ~/ tempRequestThreshold;

    if (requestMoreData && pageToRequest > currentPage) {
      currentPage = pageToRequest;
      showLoadingIndicator();

      var newItems = await Search.fetchSearchProducts(searchState, currentLocation.latitude, currentLocation.longitude,
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
