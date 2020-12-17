import 'package:cached_network_image/cached_network_image.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:overlay_support/overlay_support.dart';
import 'package:provider/provider.dart';
import 'package:rebloc/rebloc.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/constants/url-config.dart';
import 'package:resold/enums/selected-tab.dart';
import 'package:resold/helpers/sms-helper.dart';
import 'package:resold/models/order.dart';
import 'package:resold/models/product.dart';
import 'package:resold/screens/messages/message.dart';
import 'package:resold/screens/order/details.dart';
import 'package:resold/screens/tabs/map.dart';
import 'package:resold/screens/tabs/orders.dart';
import 'package:resold/screens/tabs/sell.dart';
import 'package:resold/screens/tabs/account.dart';
import 'package:resold/screens/tabs/search.dart';
import 'package:resold/screens/messages/inbox.dart';
import 'package:resold/services/magento.dart';
import 'package:resold/services/resold-firebase.dart';
import 'package:resold/services/resold-rest.dart';
import 'package:resold/state/actions/set-customer.dart';
import 'package:resold/state/actions/set-orders-state.dart';
import 'package:resold/state/actions/set-selected-tab.dart';
import 'package:resold/state/app-state.dart';
import 'package:resold/state/screens/account-state.dart';
import 'package:resold/state/screens/orders-state.dart';
import 'package:resold/state/screens/search-state.dart';
import 'package:resold/state/screens/sell/sell-image-state.dart';
import 'package:resold/state/screens/sell/sell-state.dart';
import 'package:resold/ui-models/product-ui-model.dart';
import 'package:resold/view-models/firebase/firebase-delivery-quote.dart';
import 'package:resold/view-models/firebase/inbox-message.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';
import 'package:resold/widgets/loading.dart';

class Home extends StatelessWidget {
  final GlobalKey<NavigatorState> navigatorKey = GlobalKey(debugLabel: 'Main Navigator');

  // This widget is the root of your application.
  @override
  Widget build(BuildContext context) {
    return ViewModelSubscriber<AppState, CustomerResponse>(
        converter: (state) => state.customer,
        builder: (context, dispatcher, customer) {
          return ViewModelSubscriber<AppState, Position>(
              converter: (state) => state.currentLocation,
              builder: (context, dispatcher, currentLocation) {
                return ViewModelSubscriber<AppState, SelectedTab>(
                    converter: (state) => state.selectedTab,
                    builder: (context, dispatcher, selectedTab) {
                      return MaterialApp(
                          navigatorKey: navigatorKey,
                          title: 'Resold',
                          theme: ThemeData(
                              primarySwatch: const MaterialColor(0xff41b8ea, {
                                50: Color.fromRGBO(25, 72, 92, .1),
                                100: Color.fromRGBO(25, 72, 92, .2),
                                200: Color.fromRGBO(25, 72, 92, .3),
                                300: Color.fromRGBO(25, 72, 92, .4),
                                400: Color.fromRGBO(25, 72, 92, .5),
                                500: Color.fromRGBO(25, 72, 92, .6),
                                600: Color.fromRGBO(25, 72, 92, .7),
                                700: Color.fromRGBO(25, 72, 92, .8),
                                800: Color.fromRGBO(25, 72, 92, .9),
                                900: Color.fromRGBO(25, 72, 92, 1)
                              }),
                              sliderTheme: SliderThemeData(
                                  valueIndicatorColor: ResoldBlue,
                                  showValueIndicator: ShowValueIndicator.never,
                                  valueIndicatorTextStyle: TextStyle(
                                    color: Colors.white,
                                    fontWeight: FontWeight.bold,
                                  ),
                                  tickMarkShape: SliderTickMarkShape.noTickMark),
                              scaffoldBackgroundColor: Colors.white,
                              brightness: Brightness.light,
                              accentColor: Colors.white,
                              primaryColor: ResoldBlue,
                              splashColor: ResoldBlue,
                              fontFamily: 'Raleway',
                              backgroundColor: Colors.white),
                          home: HomePage(
                              customer: customer,
                              currentLocation: currentLocation,
                              selectedTab: selectedTab,
                              dispatcher: dispatcher,
                              firebaseMessaging: FirebaseMessaging(),
                              navigatorKey: navigatorKey));
                    });
              });
        });
  } // end function build
} // end class Home

class HomePageState extends State<HomePage> {
  CustomerResponse customer;
  Position currentLocation;
  SelectedTab selectedTab;
  List<Product> results = List<Product>();
  final smsHelper = SmsHelper();
  final Function dispatcher;
  final FirebaseMessaging firebaseMessaging;
  final GlobalKey<NavigatorState> navigatorKey;
  final GlobalKey<ScaffoldState> scaffoldKey = GlobalKey<ScaffoldState>();

  HomePageState(
      {this.customer,
      this.currentLocation,
      this.selectedTab,
      this.dispatcher,
      this.firebaseMessaging,
      this.navigatorKey});

  @override
  void initState() {
    super.initState();
    setupPushNotifications();
  } // end function initState

  @override
  Widget build(BuildContext context) {
    onBuild();
    return Scaffold(
      key: scaffoldKey,
      appBar: AppBar(
        title: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Align(
                alignment: Alignment.centerLeft,
                child: Image.asset('assets/images/resold-white-logo.png', width: 145, height: 145)),
            Align(
              alignment: Alignment.centerRight,
              child: InkWell(
                child: StreamBuilder(
                    stream: ResoldFirebase.getUnreadMessageCount(customer.id),
                    builder: (context, snapshot) {
                      if (snapshot.hasData && snapshot.data.documents.length != 0) {
                        return Stack(
                          children: <Widget>[
                            Icon(Icons.message, color: Colors.white),
                            new Positioned(
                              right: 0,
                              child: new Container(
                                  padding: EdgeInsets.all(1),
                                  decoration: new BoxDecoration(
                                    color: Colors.red,
                                    borderRadius: BorderRadius.circular(6),
                                  ),
                                  constraints: BoxConstraints(
                                    minWidth: 12,
                                    minHeight: 12,
                                  ),
                                  child: Text(
                                    '${snapshot.data.documents.length}',
                                    style: new TextStyle(
                                      color: Colors.white,
                                      fontSize: 8,
                                    ),
                                    textAlign: TextAlign.center,
                                  )),
                            )
                          ],
                        );
                      } else {
                        return Icon(Icons.message, color: Colors.white);
                      } // end if we have unread message count
                    }),
                onTap: () {
                  Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (context) =>
                              InboxPage(customer: customer, currentLocation: currentLocation, dispatcher: dispatcher)));
                },
              ),
              // child: Icon(Icons.message, color: Colors.white),
            )
          ],
        ),
        iconTheme: IconThemeData(
          color: Colors.white, //change your color here
        ),
        backgroundColor: ResoldBlue,
      ),
      body: Center(
        child: getContent(context, selectedTab),
      ),
      bottomNavigationBar: BottomNavigationBar(
        type: BottomNavigationBarType.fixed,
        items: <BottomNavigationBarItem>[
          BottomNavigationBarItem(icon: Icon(Icons.home), label: 'Home'),
          BottomNavigationBarItem(icon: Icon(Icons.map), label: 'Map'),
          BottomNavigationBarItem(icon: Icon(Icons.attach_money), label: 'Sell'),
          BottomNavigationBarItem(icon: Icon(MdiIcons.carMultiple), label: 'Deliveries'),
          BottomNavigationBarItem(icon: Icon(Icons.person), label: 'Account')
        ],
        currentIndex: selectedTab.index,
        backgroundColor: Colors.white,
        fixedColor: ResoldBlue,
        unselectedItemColor: Colors.black,
        onTap: (int index) => dispatcher(SetSelectedTabAction(SelectedTab.values[index])),
      ),
    );
  } // end function build

  Widget getContent(BuildContext context, SelectedTab selectedTab) {
    switch (selectedTab) {
      case SelectedTab.home:
      case SelectedTab.map:
        return ViewModelSubscriber<AppState, SearchState>(
            converter: (state) => state.searchState,
            builder: (context, dispatcher, searchState) {
              return StreamBuilder<List<Product>>(
                  initialData: searchState.initialProducts,
                  stream: searchState.searchStream.stream,
                  builder: (context, snapshot) {
                    if (searchState.currentPage == 0) {
                      results.clear();
                    } // end if current page == 0
                    if (snapshot.hasData) {
                      results.addAll(snapshot.data);
                    } // end if we have stream data
                    return ChangeNotifierProvider<ProductUiModel>(
                        create: (_) =>
                            ProductUiModel(currentLocation: currentLocation, searchState: searchState, items: results),
                        child: Consumer<ProductUiModel>(builder: (context, model, child) {
                          if (selectedTab == SelectedTab.map) {
                            return MapPage(
                                customer: customer,
                                searchState: searchState,
                                results: results,
                                currentLocation: currentLocation,
                                dispatcher: dispatcher);
                          } else {
                            return SearchPage(
                                customer: customer,
                                searchState: searchState,
                                results: results,
                                currentLocation: currentLocation,
                                dispatcher: dispatcher,
                                handleItemCreated: model.handleItemCreated);
                          } // end if map tab
                        }));
                  });
            });
      case SelectedTab.sell:
        return ViewModelSubscriber<AppState, SellState>(
            converter: (state) => state.sellState,
            builder: (context, dispatcher, sellState) {
              return ViewModelSubscriber<AppState, SellImageState>(
                  converter: (state) => state.sellState.imageState,
                  builder: (context, dispatcher, imageState) {
                    return SellPage(
                        customer: customer,
                        currentLocation: currentLocation,
                        listingTitleController: sellState.listingTitleController,
                        priceController: sellState.priceController,
                        detailsController: sellState.detailsController,
                        selectedCondition: sellState.selectedCondition,
                        selectedCategory: sellState.selectedCategory,
                        selectedItemSize: sellState.selectedItemSize,
                        currentFormStep: sellState.currentFormStep,
                        error: sellState.error,
                        focusState: sellState.focusState,
                        imageState: imageState,
                        dispatcher: dispatcher);
                  });
            });
      case SelectedTab.orders:
        return ViewModelSubscriber<AppState, OrdersState>(
            converter: (state) => state.ordersState,
            builder: (context, dispatcher, ordersState) {
              return ViewModelSubscriber<AppState, List<Order>>(
                  converter: (state) => state.ordersState.purchasedOrders,
                  builder: (context, dispatcher, purchasedOrders) {
                    return ViewModelSubscriber<AppState, List<Order>>(
                        converter: (state) => state.ordersState.soldOrders,
                        builder: (context, dispatcher, soldOrders) {
                          return ViewModelSubscriber<AppState, List<FirebaseDeliveryQuote>>(
                              converter: (state) => state.ordersState.requestedDeliveries,
                              builder: (context, dispatcher, requestedDeliveries) {
                                purchasedOrders.sort((Order a, Order b) => b.created.compareTo(a.created));
                                soldOrders.sort((Order a, Order b) => b.created.compareTo(a.created));
                                return OrdersPage(
                                    customer: customer,
                                    purchasedOrders: purchasedOrders,
                                    soldOrders: soldOrders,
                                    requestedDeliveries: requestedDeliveries,
                                    dispatcher: dispatcher);
                              });
                        });
                  });
            });
      case SelectedTab.account:
        return ViewModelSubscriber<AppState, AccountState>(
            converter: (state) => state.accountState,
            builder: (context, dispatcher, accountState) {
              return ViewModelSubscriber<AppState, List<Product>>(
                  converter: (state) => state.accountState.forSaleProducts,
                  builder: (context, dispatcher, forSaleProducts) {
                    return ViewModelSubscriber<AppState, List<Product>>(
                        converter: (state) => state.accountState.soldProducts,
                        builder: (context, dispatcher, soldProducts) {
                          soldProducts.sort((Product a, Product b) => b.id.compareTo(a.id));
                          return AccountPage(
                              customer: customer,
                              currentLocation: currentLocation,
                              vendor: accountState.vendor,
                              forSaleProducts: forSaleProducts,
                              soldProducts: soldProducts,
                              displayForSale: accountState.displayForSale,
                              dispatcher: dispatcher);
                        });
                  });
            });
      default:
        return Text('Unknown tab');
    } // end switch on selected tab
  } // end function getContent

  void setupPushNotifications() async {
    Future.delayed(Duration(seconds: 5), () async {
      // handle Firebase push notifications
      firebaseMessaging.configure(
        onMessage: (Map<String, dynamic> message) async {
          // display notification when app in foreground
          var notification = message['notification'];
          var data = message['data'];
          if (notification == null ||
              data == null ||
              notification['title'] == null ||
              notification['body'] == null ||
              data['image'] == null) {
            return;
          } // end if notification == null

          // check if we need to send twilio notification
          if (data['approachingPickup'] == 'true') {
            await smsHelper.sendSMS(
                customer.addresses[0].telephone, 'Driver is approaching to pickup your ${notification['title']}.');
          } else if (data['approachingDropoff'] == 'true') {
            await smsHelper.sendSMS(
                customer.addresses[0].telephone, 'Driver is approaching to dropoff your ${notification['title']}.');
          } // end if approaching pickup message

          // check if we need to update orders state
          if (data['orderUpdate'] == 'true') {
            dispatcher(SetOrdersStateAction(await OrdersState.initialState(customer)));
          } else if (Navigator.canPop(context)) {
            return;
          } // end if order update notification

          // todo: check if global key chat id == message chat id
          // todo: pass message chat ID to state when opening a message

          showOverlayNotification((context) {
            return GestureDetector(
              child: Card(
                margin: const EdgeInsets.symmetric(horizontal: 4),
                child: SafeArea(
                  child: ListTile(
                    leading: SizedBox.fromSize(
                        size: const Size(40, 40),
                        child: ClipOval(
                            child: CachedNetworkImage(
                          imageUrl: baseProductImagePath + data['image'],
                        ))),
                    title: Text(notification['title']),
                    subtitle: Text(notification['body']),
                    trailing: IconButton(
                        icon: Icon(Icons.close),
                        onPressed: () async {
                          OverlaySupportEntry.of(context).dismiss();
                        }),
                  ),
                ),
              ),
              onTap: () async {
                navigateFromNotification(context, data);
              },
            );
          }, duration: Duration(milliseconds: 6000));
        },
        onLaunch: (Map<String, dynamic> message) async {
          var data = message['data'];
          navigateFromNotification(context, data);
        },
        onResume: (Map<String, dynamic> message) async {
          var data = message['data'];
          navigateFromNotification(context, data);
        },
      );
      await firebaseMessaging.requestNotificationPermissions();
      firebaseMessaging.onTokenRefresh.listen((String newToken) {
        customer.deviceToken = newToken;
        ResoldFirebase.createOrUpdateUser(customer);
        dispatcher(SetCustomerAction(customer));
      });
    });
  } // end function setupPushNotifications

  Future navigateFromNotification(BuildContext context, dynamic data) async {
    showDialog(
        context: context,
        builder: (BuildContext context) {
          return Center(child: Loading());
        });
    var chatId = data['chatId'];
    if (chatId != null) {
      // normal message notification
      InboxMessage inboxMessage = await ResoldFirebase.getUserInboxMessage(chatId);
      CustomerResponse toCustomer = await Magento.getCustomerById(inboxMessage.toId);

      // open message page
      Navigator.of(scaffoldKey.currentContext, rootNavigator: true).push(MaterialPageRoute(
          builder: (context) => MessagePage(
              fromCustomer: customer,
              toCustomer: toCustomer,
              currentLocation: currentLocation,
              product: inboxMessage.product,
              chatId: chatId,
              dispatcher: dispatcher)));
    } else {
      // delivery event notification
      int orderId = int.tryParse(data['orderId']);
      int productId = int.tryParse(data['productId']);

      // fetch order and product
      Order order = await Magento.getOrderById(orderId);
      Product product = await ResoldRest.getProduct(customer.token, productId);

      // navigate to order page
      Navigator.of(scaffoldKey.currentContext, rootNavigator: true)
          .push(MaterialPageRoute(builder: (context) => OrderDetails(order: order, product: product, isSeller: false)));
    } // end if type is message
    Navigator.of(context, rootNavigator: true).pop('dialog');
  } // end function navigateFromNotification

  void onBuild() {
    customer = widget.customer;
    currentLocation = widget.currentLocation;
    selectedTab = widget.selectedTab;
  } // end function onBuild

} // end class HomePageState

class HomePage extends StatefulWidget {
  final CustomerResponse customer;
  final Position currentLocation;
  final SelectedTab selectedTab;
  final Function dispatcher;
  final FirebaseMessaging firebaseMessaging;
  final GlobalKey<NavigatorState> navigatorKey;

  HomePage(
      {this.customer,
      this.currentLocation,
      this.selectedTab,
      this.dispatcher,
      this.firebaseMessaging,
      this.navigatorKey,
      Key key})
      : super(key: key);

  @override
  HomePageState createState() => HomePageState(
      customer: customer,
      currentLocation: currentLocation,
      selectedTab: selectedTab,
      dispatcher: dispatcher,
      firebaseMessaging: firebaseMessaging,
      navigatorKey: navigatorKey);
} // end class HomePage
