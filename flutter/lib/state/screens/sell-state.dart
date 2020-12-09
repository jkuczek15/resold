import 'package:flutter/material.dart';
import 'package:resold/state/screens/sell/sell-focus-state.dart';

class SellState {
  TextEditingController listingTitleController;
  TextEditingController priceController;
  TextEditingController detailsController;
  int selectedCategory;
  int selectedCondition;
  int selectedItemSize;
  int currentFormStep;
  PageController formPageViewController;
  SellFocusState focusState;

  SellState(
      {this.listingTitleController,
      this.priceController,
      this.detailsController,
      this.selectedCategory,
      this.selectedCondition,
      this.selectedItemSize,
      this.currentFormStep,
      this.formPageViewController,
      this.focusState});
}
