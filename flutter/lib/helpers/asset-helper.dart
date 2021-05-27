import 'dart:typed_data';
import 'dart:ui';

import 'package:flutter/services.dart';
import 'dart:ui' as ui;

class AssetHelper {
  static Future<Uint8List> getBytesFromAsset(String path, ImageByteFormat format, int width) async {
    ByteData data = await rootBundle.load(path);
    ui.Codec codec = await ui.instantiateImageCodec(data.buffer.asUint8List(), targetWidth: width);
    ui.FrameInfo fi = await codec.getNextFrame();
    return (await fi.image.toByteData(format: format)).buffer.asUint8List();
  } // end function getBytesFromAsset
}
