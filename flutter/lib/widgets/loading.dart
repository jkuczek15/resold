import 'package:flutter/material.dart';
import 'package:resold/constants/ui-constants.dart';

class Loading extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Container(
        child: Center(
      child: CircularProgressIndicator(backgroundColor: ResoldBlue),
    ));
  }
}
