import 'package:flutter/material.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/models/product.dart';
import 'package:resold/services/resold.dart' as resold;

class ProductViewModel extends ChangeNotifier {

  static const int ItemRequestThreshold = 20;
  static int currentPage = 0;
  List<Product> items;
  int lastLoadingIndex = 0;

  ProductViewModel (List<Product> data) {
    items = data;
  }

  Future handleItemCreated(int index) async {
    var itemPosition = index + 1;
    var tempRequestThreshold = ItemRequestThreshold - 1;
    var requestMoreData = itemPosition % tempRequestThreshold == 0 && itemPosition != 0;
    var pageToRequest = itemPosition ~/ tempRequestThreshold;

    if (requestMoreData && pageToRequest > currentPage) {
      currentPage = pageToRequest;
      showLoadingIndicator();

      var newItems = await resold.Api.fetchProducts(offset: pageToRequest * ItemRequestThreshold);
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