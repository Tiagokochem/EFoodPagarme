import 'dart:async';
import 'dart:io';
import 'package:efood_multivendor/controller/auth_controller.dart';
import 'package:efood_multivendor/controller/cart_controller.dart';
import 'package:efood_multivendor/controller/localization_controller.dart';
import 'package:efood_multivendor/controller/splash_controller.dart';
import 'package:efood_multivendor/controller/theme_controller.dart';
import 'package:efood_multivendor/controller/wishlist_controller.dart';
import 'package:efood_multivendor/data/model/body/deep_link_body.dart';
import 'package:efood_multivendor/data/model/body/notification_body.dart';
import 'package:efood_multivendor/helper/notification_helper.dart';
import 'package:efood_multivendor/helper/responsive_helper.dart';
import 'package:efood_multivendor/helper/route_helper.dart';
import 'package:efood_multivendor/theme/dark_theme.dart';
import 'package:efood_multivendor/theme/light_theme.dart';
import 'package:efood_multivendor/util/app_constants.dart';
import 'package:efood_multivendor/util/messages.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:flutter_facebook_auth/flutter_facebook_auth.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:get/get.dart';
import 'package:url_strategy/url_strategy.dart';
import 'helper/get_di.dart' as di;

final FlutterLocalNotificationsPlugin flutterLocalNotificationsPlugin = FlutterLocalNotificationsPlugin();

Future<void> main() async {
  if(ResponsiveHelper.isMobilePhone()) {
    HttpOverrides.global = MyHttpOverrides();
  }
  setPathUrlStrategy();
  WidgetsFlutterBinding.ensureInitialized();

  DeepLinkBody? linkBody;

  if(GetPlatform.isWeb) {
    await Firebase.initializeApp(options: const FirebaseOptions(
      apiKey: 'AIzaSyCeaw_gVN0iQwFHyuF8pQ6PbVDmSVQw8AY',
      appId: '1:1049699819506:web:a4b5e3bedc729aab89956b',
      messagingSenderId: '1049699819506',
      projectId: 'stackfood-bd3ee',
    ));
  }else {
    await Firebase.initializeApp();

    // try {
    //   String initialLink = await getInitialLink();
    //   print('======initial link ===>  $initialLink');
    //   if(initialLink != null) {
    //     _linkBody = LinkConverter.convertDeepLink(initialLink);
    //   }
    // } on PlatformException {}
  }

  Map<String, Map<String, String>> languages = await di.init();

  NotificationBody? body;
  try {
    if (GetPlatform.isMobile) {
      final RemoteMessage? remoteMessage = await FirebaseMessaging.instance.getInitialMessage();
      if (remoteMessage != null) {
        body = NotificationHelper.convertNotification(remoteMessage.data);
      }
      await NotificationHelper.initialize(flutterLocalNotificationsPlugin);
      FirebaseMessaging.onBackgroundMessage(myBackgroundMessageHandler);
    }
  }catch(_) {}

  if (ResponsiveHelper.isWeb()) {
    await FacebookAuth.instance.webAndDesktopInitialize(
      appId: "452131619626499",
      cookie: true,
      xfbml: true,
      version: "v13.0",
    );
  }
  runApp(MyApp(languages: languages, body: body, linkBody: linkBody));
}

class MyApp extends StatelessWidget {
  final Map<String, Map<String, String>>? languages;
  final NotificationBody? body;
  final DeepLinkBody? linkBody;
  const MyApp({Key? key, required this.languages, required this.body, required this.linkBody}) : super(key: key);

  void _route() {
    Get.find<SplashController>().getConfigData().then((bool isSuccess) async {
      if (isSuccess) {
        if (Get.find<AuthController>().isLoggedIn()) {
          Get.find<AuthController>().updateToken();
          await Get.find<WishListController>().getWishList();
        }
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    if(GetPlatform.isWeb) {
      Get.find<SplashController>().initSharedData();
      Get.find<CartController>().getCartData();
      _route();
    }

    return GetBuilder<ThemeController>(builder: (themeController) {
      return GetBuilder<LocalizationController>(builder: (localizeController) {
        return GetBuilder<SplashController>(builder: (splashController) {
          return (GetPlatform.isWeb && splashController.configModel == null) ? const SizedBox() : GetMaterialApp(
            title: AppConstants.appName,
            debugShowCheckedModeBanner: false,
            navigatorKey: Get.key,
            scrollBehavior: const MaterialScrollBehavior().copyWith(
              dragDevices: {PointerDeviceKind.mouse, PointerDeviceKind.touch},
            ),
            theme: themeController.darkTheme ? dark : light,
            locale: localizeController.locale,
            translations: Messages(languages: languages),
            fallbackLocale: Locale(AppConstants.languages[0].languageCode!, AppConstants.languages[0].countryCode),
            initialRoute: GetPlatform.isWeb ? RouteHelper.getInitialRoute() : RouteHelper.getSplashRoute(body, linkBody),
            getPages: RouteHelper.routes,
            defaultTransition: Transition.topLevel,
            transitionDuration: const Duration(milliseconds: 500),
          );
        });
      });
    });
  }
}

class MyHttpOverrides extends HttpOverrides {
  @override
  HttpClient createHttpClient(SecurityContext? context) {
    return super.createHttpClient(context)..badCertificateCallback = (X509Certificate cert, String host, int port) => true;
  }
}
