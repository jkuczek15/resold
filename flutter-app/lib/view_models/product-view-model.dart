import 'package:flutter/material.dart';
import '../constants/ui-constants.dart';
import '../models/product.dart';
import '../services/resold.dart' as resold;

class ProductViewModel extends ChangeNotifier {

  static const int ItemRequestThreshold = 20;
  static int currentPage = 0;
  List<Product> items;

  ProductViewModel (List<Product> data) {
    items = data;
  }

  Future handleItemCreated(int index) {
    var itemPosition = index + 1;
    var requestMoreData = itemPosition % ItemRequestThreshold == 0 && itemPosition != 0;
    var pageToRequest = itemPosition ~/ ItemRequestThreshold;

    if (requestMoreData && pageToRequest > currentPage) {

      resold.Api.fetchProducts(page: pageToRequest).then((newItems) => items.addAll(newItems));

      print('should request more data');
      currentPage = pageToRequest;
      // TODO: Show loading indicator, Request more data and hide loading indicator
    }
  }

  void _showLoadingIndicator() {
//    items.add(LoadingIndicatorTitle);
    notifyListeners();
  }

  void _removeLoadingIndicator() {
//    _items.remove(LoadingIndicatorTitle);
    notifyListeners();
  }
}