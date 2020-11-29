import 'dart:async';

import 'package:flutter/material.dart';
import 'package:resold/enums/sort.dart';
import 'package:resold/models/product.dart';

class SearchState {
  TextEditingController textController;
  StreamController<List<Product>> searchStream;
  List<Product> initialProducts;
  String selectedCategory;
  String selectedCondition;
  Sort selectedSort;
  String distance;

  SearchState(
      {this.textController,
      this.searchStream,
      this.initialProducts,
      this.selectedCategory,
      this.selectedCondition,
      this.selectedSort,
      this.distance});
}
