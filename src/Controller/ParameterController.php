<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\Controller;

use Jihel\Plugin\DynamicParameterBundle\DependencyInjection\Cache\ParameterCache;
use Jihel\Plugin\DynamicParameterBundle\DependencyInjection\Loader\ParameterLoader;
use Jihel\Plugin\DynamicParameterBundle\Entity\Parameter;
use Jihel\Plugin\DynamicParameterBundle\Form\Type\ParameterFormType;

use Jihel\Plugin\DynamicParameterBundle\Repository\ParameterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

// Annotation
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class ParameterController
 *
 * @author Joseph LEMOINE <lemoine.joseph@gmail.com>
 * @link http://www.joseph-lemoine.fr
 */
class ParameterController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $m          = $this->getDoctrine()->getManager();
        $parameter  = new Parameter();
        $form       = $this->createForm(new ParameterFormType(), $parameter);

        if ('POST' === $request->getMethod() && $form->handleRequest($request)->isValid()) {
            $m->persist($parameter);
            $m->flush();

            $this->rebuildCache();

            $session = $request->getSession();
            $session->getFlashBag()->add('success', 'jihel.plugin.dynamic_parameter.parameter.create.success');

            return $this->redirect($this->generateUrl('JihelPluginDynamicParameterBundle_parameter_index'));
        }

        $allowedNamespaces  = $this->container->getParameter('jihel.plugin.dynamic_parameter.allowed_namespaces');
        $deniedNamespaces   = $this->container->getParameter('jihel.plugin.dynamic_parameter.denied_namespaces');

        /** @var ParameterRepository $parameterRepository */
        $parameterRepository = $m->getRepository('JihelPluginDynamicParameterBundle:Parameter');
        $entities = $parameterRepository->findByNamespace($allowedNamespaces, $deniedNamespaces);

        return $this->render('JihelPluginDynamicParameterBundle:Parameter:index.html.twig', array(
            'allowedNamespaces' => $allowedNamespaces,
            'deniedNamespaces'  => $deniedNamespaces,
            'form'              => $form->createView(),
            'deleteForms'       => $this->createDeleteForms($entities),
            'entities'          => $entities,
        ));
    }

    /**
     * @param Request $request
     * @param Parameter $parameter
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @ParamConverter("parameter", class="JihelPluginDynamicParameterBundle:Parameter")
     */
    public function editAction(Request $request, Parameter $parameter)
    {
        $form  = $this->createForm(new ParameterFormType(), $parameter);

        if ('POST' === $request->getMethod() && $form->handleRequest($request)->isValid()) {
            $m = $this->getDoctrine()->getManager();
            $m->persist($parameter);
            $m->flush();

            $this->rebuildCache();

            $session = $request->getSession();
            $session->getFlashBag()->add('success', 'jihel.plugin.dynamic_parameter.parameter.update.success');

            return $this->redirect($this->generateUrl('JihelPluginDynamicParameterBundle_parameter_index'));
        }

        return $this->render('JihelPluginDynamicParameterBundle:Parameter:edit.html.twig', array(
            'form'      => $form->createView(),
            'entity'    => $parameter,
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @ParamConverter("parameter", class="JihelPluginDynamicParameterBundle:Parameter")
     */
    public function deleteAction(Request $request, Parameter $parameter)
    {
        $session = $request->getSession();
        $form = $this->createDeleteForm($parameter);
        if ($form->handleRequest($request)->isValid()) {
            $m = $this->getDoctrine()->getManager();
            $m->remove($parameter);
            $m->flush();

            $this->rebuildCache();

            $session->getFlashBag()->add('success', 'jihel.plugin.dynamic_parameter.parameter.delete.success');
        } else {
            $session->getFlashBag()->add('success', 'jihel.plugin.dynamic_parameter.parameter.delete.error');
        }
        return $this->redirect($this->generateUrl('JihelPluginDynamicParameterBundle_parameter_index'));
    }

    /**
     * @param array|Parameter[] $entities
     * @return array
     */
    protected function createDeleteForms(array $entities = array())
    {
        $out = array();
        if (count($entities)) {
            foreach ($entities as $entity) {
                $out[$entity->getId()] = $this->createDeleteForm($entity);
                $out[$entity->getId()] = $out[$entity->getId()]->createView();
            }
        }
        return $out;
    }

    /**
     * @param Parameter $parameter
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm(Parameter $parameter)
    {
        return $this->createFormBuilder($parameter, array(
                'action' => $this->generateUrl('JihelPluginDynamicParameterBundle_parameter_delete', array(
                    'id' => $parameter->getId(),
                )),
            ))
            ->getForm()
        ;
    }

    /**
     * @return int
     * @throws \Jihel\Plugin\DynamicParameterBundle\DependencyInjection\Cache\Exception\UnwritableCacheException
     */
    protected function rebuildCache()
    {
        /** @var ParameterLoader $parameterLoader */
        $parameterLoader = $this->get('jihel.plugin.dynamic_parameter.loader.parameter');
        $dynamicParameters = $parameterLoader->load(true);

        /** @var ParameterCache $parameterCache */
        $parameterCache = $this->get('jihel.plugin.dynamic_parameter.cache.parameter');
        return $parameterCache->createCache($dynamicParameters, true);
    }
}
