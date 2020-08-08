import 'package:flutter/material.dart';

class Loading extends StatelessWidget {
  const Loading();

  @override
  Widget build(BuildContext context) {
    return Container(
      child: Center(
        child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)),
      ),
      color: Colors.white.withOpacity(0.8),
    );
  }
}