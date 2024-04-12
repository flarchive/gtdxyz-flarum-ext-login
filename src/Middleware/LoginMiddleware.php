<?php

namespace Gtdxyz\Login\Middleware;

use Flarum\Http\RequestUtil;
use Flarum\Locale\Translator;
use Flarum\Foundation\ValidationException;
use Flarum\Settings\SettingsRepositoryInterface;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Illuminate\Support\Arr;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LoginMiddleware implements MiddlewareInterface
{
    private $translator;
    private $settings;

    public function __construct(Translator $translator, SettingsRepositoryInterface $settings)
    {
        $this->translator = $translator;
        $this->settings = $settings;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        
        $actor = RequestUtil::getActor($request);
        
        if ($request->getUri()->getPath() === '/token' && $request->getMethod() == 'POST') {

            $loginType = $this->settings->get('gtdxyz-login.type', 'default');
            $body = $request->getParsedBody();
            
            $params = Arr::only($body, ['identification', 'password', 'remember']);
            if(count($params) != 3){
                throw new ValidationException([
                    'message' => $this->translator->trans('gtdxyz-login.forum.error.failed')
                ]);
            }

            $validator = new EmailValidator();
            $check = $validator->isValid($params['identification'], new RFCValidation());

            // check email
            if($loginType == 'email' && !$check){
                throw new ValidationException([
                    'message' => $this->translator->trans('gtdxyz-login.forum.error.email_failed')
                ]);
            }

            if($loginType == 'username' && $check){
                throw new ValidationException([
                    'message' => $this->translator->trans('gtdxyz-login.forum.error.username_failed')
                ]);
            }

        }

        $response = $handler->handle($request);

        return $response;
    }
}
