<?php

namespace YZ\SupervisorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * SupervisorController
 */
class SupervisorController extends Controller
{
    private static $publicInformations = ['description', 'group', 'name', 'state', 'statename'];
    /**
     * indexAction
     */
    public function indexAction()
    {
        $supervisorManager = $this->get('supervisor.manager');

        return $this->render('YZSupervisorBundle:Supervisor:list.html.twig', array(
            'supervisors' => $supervisorManager->getSupervisors(),
        ));
    }

    /**
     * startStopProcessAction
     *
     * @param string $start 1 to start, 0 to stop it
     * @param string $key   The key to retrieve a Supervisor object
     * @param string $name  The name of a process
     * @param string $group The group of a process
     *
     * @return Symfony\Component\HttpFoundation\Response represents an HTTP response.
     */
    public function startStopProcessAction($start, $key, $name, $group)
    {
        $supervisor = $this->get('supervisor.manager')->getSupervisorByKey($key);
        if (!$supervisor) {
            throw new \Exception('Supervisor not found');
        }

        $success = true;
        $process = $supervisor->getProcessByNameAndGroup($name, $group);
        try {
            if ($start == "1") {
                $success = $process->startProcess();
            } elseif ($start == "0") {
                $success = $process->stopProcess();
            } else {
                $success = false;
            }

        } catch (\Exception $e) {
            $success = false;
            $this->get('session')->getFlashBag()->add('error', 'Erreur lors de l\'arret du processus.');
        }

        if (!$success) {
            $this->get('session')->getFlashBag()->add('error', 'Erreur lors '.($start == "1" ? 'du lancement' : 'de l\'arret').' du processus.');
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            $processInfo = $process->getProcessInfo();
            $res = json_encode([
                'success'       => $success,
                'message'       => implode(', ', $this->get('session')->getFlashBag()->get('error', array())),
                'processInfo'   => $processInfo
            ]);

            return new Response($res, 200, [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-store',
            ]);
        }

        return $this->redirect($this->generateUrl('supervisor'));
    }

    /**
     * startStopAllProcessesAction
     *
     * @param string $start 1 to start, 0 to stop it
     * @param string $key   The key to retrieve a Supervisor object
     */
    public function startStopAllProcessesAction($start, $key)
    {
        $supervisor = $this->get('supervisor.manager')->getSupervisorByKey($key);
        if (!$supervisor) {
            throw new \Exception('Supervisor not found');
        }

        if ($start == "1") {
            if ($supervisor->startAllProcesses() != true) {
                $this->get('session')->getFlashBag()->add('error', 'Erreur lors du lancement de tous processus.');
            }
        } elseif ($start == "0") {
            if ($supervisor->stopAllProcesses() != true) {
                $this->get('session')->getFlashBag()->add('error', 'Erreur lors de l\'arret de tous les processus.');
            }
        }

        return $this->redirect($this->generateUrl('supervisor'));
    }

    /**
     * showSupervisorLogAction
     *
     * @param string $key The key to retrieve a Supervisor object
     */
    public function showSupervisorLogAction($key)
    {
        $supervisorManager = $this->get('supervisor.manager');
        $supervisor = $supervisorManager->getSupervisorByKey($key);

        if (!$supervisor) {
            throw new \Exception('Supervisor not found');
        }

        $logs = $supervisor->readLog(0, 0);

        return $this->render('YZSupervisorBundle:Supervisor:showLog.html.twig', array(
            'log' => $logs,
        ));
    }

    /**
     * clearSupervisorLogAction
     *
     * @param string $key The key to retrieve a Supervisor object
     */
    public function clearSupervisorLogAction($key)
    {
        $supervisorManager = $this->get('supervisor.manager');
        $supervisor = $supervisorManager->getSupervisorByKey($key);

        if (!$supervisor) {
            throw new \Exception('Supervisor not found');
        }

        if ($supervisor->clearLog() != true) {
            $this->get('session')->getFlashBag()->add('error', 'Erreur lors de la suppression des logs.');
        }

        return $this->redirect($this->generateUrl('supervisor'));
    }

    /**
     * showProcessLogAction
     *
     * @param string $key   The key to retrieve a Supervisor object
     * @param string $name  The name of a process
     * @param string $group The group of a process
     */
    public function showProcessLogAction($key, $name, $group)
    {
        $supervisorManager = $this->get('supervisor.manager');
        $supervisor = $supervisorManager->getSupervisorByKey($key);
        $process = $supervisor->getProcessByNameAndGroup($name, $group);

        if (!$supervisor) {
            throw new \Exception('Supervisor not found');
        }

        $result = $process->tailProcessStdoutLog(0, 1);
        $stdout = $process->tailProcessStdoutLog(0, $result[1]);

        return $this->render('YZSupervisorBundle:Supervisor:showLog.html.twig', array(
            'log' => $stdout[0],
        ));
    }

    /**
     * showProcessLogErrAction
     *
     * @param string $key   The key to retrieve a Supervisor object
     * @param string $name  The name of a process
     * @param string $group The group of a process
     */
    public function showProcessLogErrAction($key, $name, $group)
    {
        $supervisorManager = $this->get('supervisor.manager');
        $supervisor = $supervisorManager->getSupervisorByKey($key);
        $process = $supervisor->getProcessByNameAndGroup($name, $group);

        if (!$supervisor) {
            throw new \Exception('Supervisor not found');
        }

        $result = $process->tailProcessStderrLog(0, 1);
        $stderr = $process->tailProcessStderrLog(0, $result[1]);

        return $this->render('YZSupervisorBundle:Supervisor:showLog.html.twig', array(
            'log' => $stderr[0],
        ));
    }

    /**
     * clearProcessLogAction
     *
     * @param string $key   The key to retrieve a Supervisor object
     * @param string $name  The name of a process
     * @param string $group The group of a process
     */
    public function clearProcessLogAction($key, $name, $group)
    {
        $supervisorManager = $this->get('supervisor.manager');
        $supervisor = $supervisorManager->getSupervisorByKey($key);
        $process = $supervisor->getProcessByNameAndGroup($name, $group);

        if (!$supervisor) {
            throw new \Exception('Supervisor not found');
        }

        if ($process->clearProcessLogs() != true) {
            $this->get('session')->getFlashBag()->add('error', 'Erreur lors de la suppression des logs.');
        }

        return $this->redirect($this->generateUrl('supervisor'));
    }

    /**
     * showProcessInfoAction
     *
     * @param string $key   The key to retrieve a Supervisor object
     * @param string $name  The name of a process
     * @param string $group The group of a process
     *
     * @return Symfony\Component\HttpFoundation\Response represents an HTTP response.
     */
    public function showProcessInfoAction($key, $name, $group)
    {
        $supervisorManager = $this->get('supervisor.manager');
        $supervisor = $supervisorManager->getSupervisorByKey($key);
        $process = $supervisor->getProcessByNameAndGroup($name, $group);

        if (!$supervisor) {
            throw new \Exception('Supervisor not found');
        }

        $infos = $process->getProcessInfo();

        if ($this->getRequest()->isXmlHttpRequest()) { 
            $processInfo = [];
            foreach (self::$publicInformations as $public) {
                $processInfo[$public] = $infos[$public];
            }

            $res = json_encode([
                'supervisor'    => $key,
                'processInfo'   => $processInfo,
                'controlLink'   => $this->generateUrl('supervisor.process.startStop', [
                    'key'   => $key, 
                    'name'  => $name, 
                    'group' => $group, 
                    'start' => ($infos['state'] == 10 || $infos['state'] == 20 ? '0' : '1')
                ])
            ]);

            return new Response($res, 200, [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-store',
            ]);
        }

        return $this->render('YZSupervisorBundle:Supervisor:showInformations.html.twig', array(
            'informations' => $infos,
        ));
    }

    /**
     * showProcessAllInfoAction
     *
     * @param string $key   The key to retrieve a Supervisor object
     *
     * @return Symfony\Component\HttpFoundation\Response represents an HTTP response.
     */
    public function showProcessInfoAllAction($key)
    {
        if (!$this->getRequest()->isXmlHttpRequest()) { 
            throw new \Exception('Ajax request expected here');
        }

        $supervisorManager = $this->get('supervisor.manager');
        $supervisor = $supervisorManager->getSupervisorByKey($key);

        if (!$supervisor) {
            throw new \Exception('Supervisor not found');
        }

        $processes = $supervisor->getProcesses();
        $processesInfo = [];
        foreach ($processes as $process) {
            $infos = $process->getProcessInfo();
            $processInfo = [];
            foreach (self::$publicInformations as $public) {
                $processInfo[$public] = $infos[$public];
            }

            $processesInfo[$infos['name']] = [
                'supervisor'    => $key,
                'processInfo'   => $processInfo,
                'controlLink'   => $this->generateUrl('supervisor.process.startStop', [
                    'key'   => $key, 
                    'name'  => $infos['name'], 
                    'group' => $infos['group'], 
                    'start' => ($infos['state'] == 10 || $infos['state'] == 20 ? '0' : '1')
                ])
            ];
        }
      
        $res = json_encode($processesInfo);

        return new Response($res, 200, [
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-store',
        ]);
    }
}
