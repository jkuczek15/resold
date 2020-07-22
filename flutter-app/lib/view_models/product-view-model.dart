import 'package:flutter/material.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/models/product.dart';
import 'package:resold/services/resold.dart' as resold;
import 'package:geolocator/geolocator.dart';

class ProductViewModel extends ChangeNotifier {

  int ItemRequestThreshold = 20;
  static int currentPage = 0;
  List<Product> items;
  int lastLoadingIndex = 0;
  Position currentLocation;

  ProductViewModel (Position currentLocation, List<Product> data) {
    items = data;
    this.currentLocation = currentLocation;
  }

  Future handleItemCreated(int index) async {
    ItemRequestThreshold = this.items.length;

    var itemPosition = index + 1;
    var tempRequestThreshold = ItemRequestThreshold - 1;
    var requestMoreData = itemPosition % tempRequestThreshold == 0 && itemPosition != 0;
    var pageToRequest = itemPosition ~/ tempRequestThreshold;

    if (requestMoreData && pageToRequest > currentPage) {
      currentPage = pageToRequest;
      showLoadingIndicator();

      var newItems = await resold.Api.fetchLocalProducts(currentLocation.latitude, currentLocation.longitude, offset: pageToRequest * ItemRequestThreshold);
      items.addAll(newItems);

      removeLoadingIndicator();
    }
  }

  void showLoadingIndicator() {
    items.add(Product(
      name: LoadingIndicatorTitle
    ));
    lastLoadingIndex = items.length-1;
    notifyListeners();
  }

  void removeLoadingIndicator() {
    items.removeAt(lastLoadingIndex);
    notifyListeners();
  }
}